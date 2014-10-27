<?php

namespace App\ProjectBundle\Form;

use App\ProjectBundle\Form\Subscriber\ProjectLockSubscriber;
use App\StatusBundle\Utils\StatusTranslator;
use App\IndustryBundle\Entity\Sector;
use App\ProjectBundle\Entity\Project;
use App\PersonBundle\Form\PersonType;
use App\ProductBundle\Form\FormMapper;
use App\ProjectBundle\Entity\ProjectRepository;
use App\ProjectBundle\Entity\ProjectOpportunity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ProjectType extends AbstractType
{
    public $em;
    protected $statusTranslator;
    protected $accountProfileId = 0;

    public function __construct(EntityManager $em, StatusTranslator $statusTranslator)
    {
        $this->em = $em;
        $this->statusTranslator = $statusTranslator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $status = $this->statusTranslator->getStatusesNames(Project::STATUS_GROUP_NAME);

        $builder->add('startDate', 'date', array(
                    'label' => 'start_date',
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr' => array('class' => 'datetimepicker')))
                ->add('name', null, array(
                    'label' => 'name',
                    'required' => true))
                ->add('status', 'choice', array(
                    'label' => 'status',
                    'choices' => $status,
                    'required' => false,
                    'empty_value' => 'Choose a status',
                    'data' => Project::STATUS_TO_PRODUCE
                ))
                ->add('workingDays', 'choice', array(
                    'choices'   => ProjectRepository::getWeekDaysList(),
                    'multiple'  => true,
                    "expanded" => true,
                    'required' => false,
                    'label' => 'working_days'
                ))
                ->add('startTime', 'time', array(
                    'label' => 'work_start_time',
                    'with_minutes' => false,                    
                ))
                ->add('endTime', 'time', array(
                    'label' => 'work_end_time',
                    'with_minutes' => false
                ))
                ->add('endDateOnLastTask', 'checkbox', array(
                    'required'  => false,
                    'label'     => 'project_end_date'))
                ->add('endDate', 'date', array(
                    'label' => 'select_end_date',
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr' => array('class' => 'datetimepicker')))
                ->add('places', 'entity', [
                    'class' => 'App\PlaceBundle\Entity\Place',
                    'required' => false,
                    'multiple' => true,
                    'select2' => true,
                    'label' => 'places',
                    'translation_domain' => 'Place'
                ])
                ->add('owner', 'entity', [
                    'label' => 'owner',
                    'required' => false,
                    'empty_value'=>'Choose an option',
                    'class' => 'App\UserBundle\Entity\User',
                    'select2' => true,
                    'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('u')->orderBy('u.username');
                    }
                ]);

        $builder->addEventSubscriber(new ProjectLockSubscriber($this->em));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProjectBundle\Entity\Project',
            'translation_domain' => 'Project'
        ));
    }

    public function getName()
    {
        return 'project_form';
    }
}