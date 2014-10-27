<?php

namespace App\TaskBundle\Form;

use App\StatusBundle\Utils\StatusTranslator;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Entity\TaskBase;
use App\TaskBundle\Form\Subscriber\TaskPresetTypeSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TaskPresetType extends AbstractType
{
    protected $statusTranslator;

    public function __construct(StatusTranslator $statusTranslator)
    {
        $this->statusTranslator = $statusTranslator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = $this->statusTranslator->getStatusesNames(Task::TYPES_GROUP_NAME);
        $statuses = $this->statusTranslator->getStatusesNames(Task::STATUS_GROUP_NAME);

        $builder
            ->add('place', 'entity', [
                'label' => 'place',
                'translation_domain' => 'Place',
                'class' => 'App\PlaceBundle\Entity\Place',
                'required' => false,
                'attr' => array('class' => 'form-control'),
            ])
            ->add('category', 'select_category', array(
                'label' => 'category',
                'translation_domain' => 'Project',
                'attr' => array('class' => 'form-control'),
                'class' => 'AppProjectBundle:Category',
                'required' => true))
            ->add('tracker', 'entity', array(
                    'label' => 'tracker',
                    'empty_value' => 'Choose a tracker',
                    'class'=>'AppTaskBundle:TaskTracker',
                    'expanded'=>false,
                    'multiple'=>false,
                    'required' => true,
                    'attr' => array('class' => 'form-control'),
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('t')->orderBy('t.value', 'ASC');
                        return $qb;}))
            ->add('name', 'text', array(
                'label' => 'name',
                'translation_domain' => 'Common',
                'required' => true))
            ->add('taskDescription', 'textarea', array(
                'label' => 'task_details',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'description',
                'translation_domain' => 'Common',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                ))
            ->add('status', 'choice', array(
                    'label' => 'status',
                    'choices' => $statuses,
                    'required' => true,
                ))
            ->add('priority', 'entity', array(
                    'label' => 'priority',
                    'empty_value' => 'Choose a priority',
                    'class'=>'AppTaskBundle:TaskPriority',
                    'expanded'=>false,
                    'multiple'=>false,
                    'required' => true,
                    'attr' => array('class' => 'form-control'),
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('tp')->orderBy('tp.value', 'ASC');
                        return $qb;}))
            ->add('assignedTo', 'entity', [
                    'required' => false,
                    'multiple' => true,
                    'select2' => true,
                    'label' => 'assigned_to',
                    'class' => 'App\PersonBundle\Entity\Person',
                    'query_builder' => function(EntityRepository $repository){
                        return $repository->getDefaultQueryBuilder();
                    }
                ])
            ->add('estimatedTime', 'interval', array(
                    'label' => 'estimated_time',
                    'required' => false, 
                ))
            ->add('estimatedPrice', 'number', array(
                'label' => 'estimated_cost',
                'required' => false
            ))
            ->add('startDate', 'datetime', array(
                'required' => false,
                'label' => 'start_date',
                'widget' => 'single_text',
                'format' => 'd-MM-yyyy HH:mm',
                'attr' => array('class' => 'datetimepicker')))
            ->add('dueDate', 'datetime', array(
                'required' => false,
                'label' => 'due_date',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'datetimepicker')))
            ->add('lag', 'interval', array(
                    'label' => 'dependency.lag',
                    'required' => false
                ))
            ->add('type', 'choice', array(
                    'label' => 'type',
                    'choices' => $types,
                    'required' => true,
                    'attr' => [
                        'class' => 'task-type'
                    ]
                ))
            ->add('order', 'number', [
                'label' => 'position_in_category',
                'required' => false
            ]);

        $builder->addEventSubscriber(new TaskPresetTypeSubscriber($this->statusTranslator));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TaskBundle\Entity\TaskPreset',
            'translation_domain' => 'Tasks'
        ));
    }

    public function getName()
    {
        return 'task_preset';
    }
}