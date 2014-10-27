<?php

namespace App\TaskBundle\Form;

use App\PersonBundle\Entity\PersonRepository;
use App\PlaceBundle\Entity\PlaceRepository;
use App\StatusBundle\Utils\StatusTranslator;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Entity\TaskBase;
use App\TaskBundle\Form\Subscriber\TaskLockSubscriber;
use App\TaskBundle\Form\Subscriber\TaskTypeSubscriber;
use App\TaskBundle\Form\TaskFileType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType {

    protected $statusTranslator;

    public function __construct(StatusTranslator $statusTranslator) {
        $this->statusTranslator = $statusTranslator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $dependencies = $this->statusTranslator->getStatusesNames(TaskBase::DEPENDENCY_GROUP_NAME);

        $builder
                ->add('tracker', 'entity', array(
                    'label' => 'tracker',
                    'empty_value' => 'Choose a tracker',
                    'class' => 'AppTaskBundle:TaskTracker',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => true,
                    'attr' => array('class' => 'form-control'),
                    'query_builder' => function(EntityRepository $repository) {
                $qb = $repository->createQueryBuilder('t')->orderBy('t.value', 'ASC');
                return $qb;
            }))
                ->add('category', 'select_category', array(
                    'label' => 'category',
                    'translation_domain' => 'Project',
                    'class' => 'AppProjectBundle:Category',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('place', 'entity', [
                    'label' => 'place',
                    'translation_domain' => 'Place',
                    'class' => 'App\PlaceBundle\Entity\Place',
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                    'query_builder' => function(PlaceRepository $repository) {
                return $repository->getDefaultQueryBuilder();
            }
                ])
                ->add('description', 'textarea', array(
                    'label' => 'description',
                    'translation_domain' => 'Common',
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'simple'),
                    'required' => false,
                ))
                ->add('priority', 'entity', array(
                    'label' => 'priority',
                    'empty_value' => 'Choose a priority',
                    'class' => 'AppTaskBundle:TaskPriority',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => true,
                    'attr' => array('class' => 'form-control'),
                    'query_builder' => function(EntityRepository $repository) {
                $qb = $repository->createQueryBuilder('tp')->orderBy('tp.value', 'ASC');
                return $qb;
            }
                ))
                ->add('assignedTo', 'entity', [
                    'label' => 'assigned_to',
                    'required' => false,
                    'multiple' => true,
                    'select2' => true,
                    'attr' => array('class' => 'form-control'),
                    'class' => 'App\PersonBundle\Entity\Person',
                    'query_builder' => function(PersonRepository $repository) {
                return $repository->getDefaultQueryBuilder();
            }
                ])
                ->add('estimatedPrice', 'number', array(
                    'label' => 'estimated_cost',
                    'required' => false
                ))
                ->add('doneRatio', 'percent', array(
                    'label' => 'done_ratio',
                    'attr' => [
                        'class' => 'input-mini'
                    ]
                ))
                ->add('startDate', 'datetime', array(
                    'required' => false,
                    'label' => 'start_date',
                    'widget' => 'single_text',
                    'format' => 'd-MM-yyyy HH:mm',
                    'attr' => array('class' => 'datetimepicker start-date')))
                ->add('dueDate', 'datetime', array(
                    'required' => false,
                    'label' => 'due_date',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy HH:mm',
                    'attr' => array('class' => 'datetimepicker due-date')))
                ->add('pid', 'entity', array(
                    'label' => 'parent_task',
                    'empty_value' => 'Choose a task',
                    'class' => 'AppTaskBundle:Task',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false,
                    'select2' => true,
                    'attr' => array('class' => 'form-control'),
                    'query_builder' => function(EntityRepository $repository) {
                $qb = $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
                return $qb;
            }
                ))
                ->add('dependency', 'choice', array(
                    'label' => 'dependency.type',
                    'empty_value' => 'Choose a dependency',
                    'required' => false,
                    'choices' => $dependencies,
                    'attr' => [
                        'class' => 'form-control task-dependency'
                    ]
                ))
                ->add('lag', 'interval', array(
                    'label' => 'dependency.lag',
                    'required' => false
                ))
                ->add('order', 'number', [
                    'label' => 'position_in_category',
                    'required' => false,
                ])
                ->add('files', 'collection', array(
                    'label' => 'files',
                    'type' => new TaskFileType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'collection image-upload'),
                    'required' => false
        ));

        $builder->addEventSubscriber(new TaskLockSubscriber($this->statusTranslator))
                ->addEventSubscriber(new TaskTypeSubscriber($this->statusTranslator));
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        /** @var Task $task */
        $task = $form->getData();

        $view->vars['dependant_tasks'] = $task->getParentOf();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\TaskBundle\Entity\Task',
            'translation_domain' => 'Tasks'
        ));
    }

    public function getName() {
        return 'task';
    }

}
