<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 06.03.14
 * Time: 14:26
 */

namespace App\ProjectBundle\Form\Subscriber;


use App\ProjectBundle\Entity\Project;
use App\ProjectBundle\Entity\ProjectOpportunityRepository;
use App\ProjectBundle\Entity\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProjectLockSubscriber implements EventSubscriberInterface
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'addFields'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $this->project = $event->getData();

        $this->addFields($event);
    }

    public function addFields(FormEvent $event)
    {
        $form = $event->getForm();

        $project = $this->project;
        $is_locked = $this->project->isProject();

        $preferredIsoChoices = $this->entityManager->getRepository('AppCurrencyBundle:Currency')->getPreferredCurrency();

        $form->add('accountProfile', 'entity', array(
                'required'  => true,
                'class' => 'App\AccountBundle\Entity\AccountProfile',
                'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('a')->orderBy('a.name', 'ASC');
                    },
                'property'  => 'name',
                'empty_value' => 'Choose an option',
                'label'     => 'vendor_company',
                'translation_domain' => 'AccountProfile',
                'disabled' => $is_locked
            ))
            ->add(
                'opportunity', 'entity', array(
                'class'        =>'App\ProjectBundle\Entity\ProjectOpportunity',
                'empty_value' => 'Choose an option',
                'required'=>true,
                'query_builder' => function(ProjectOpportunityRepository $repository) use ($project){
                        return ($project->getAccountProfile() !== null) ?
                            $repository->getNotUsedOpportunitiesQueryBuilder($project->getAccountProfile(), $project):
                            $repository->getNotUsedOpportunitiesQueryBuilder();
                    },
                'disabled' => $is_locked,
                'label' => 'opportunity'
            ))
            ->add('termCondition', 'textarea', array(
                'label' => 'terms_and_conditions',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false,
                'disabled' => $is_locked
            ))
            ->add('description', 'textarea', array(
                'label' => 'description',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false,
                'disabled' => $is_locked
            ))
            ->add('depositAmount', 'percent', array(
                'label' => 'deposit_amount',
                'precision' => 4,
                'required' => false,
                'disabled' => $is_locked
            ))
            ->add('invoiceDeliveryType', 'choice', array(
                'label' => 'invoice_delivery_type',
                'choices' => ProjectRepository::getInvoiceDeliveryTypeList(),
                'disabled' => $is_locked
            ))
            ->add('currency', 'entity', array(
                'class'=>'AppCurrencyBundle:Currency',
                'expanded'=>false,
                'multiple'=>false,
                'required'  => true,
                'query_builder'=>function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('sp')->orderBy('sp.name', 'ASC');
                    },
                'preferred_choices'=> $preferredIsoChoices,
                'disabled' => $is_locked,
                'label' => 'currency',
                'translation_domain' => 'Currency'
            ));
    }

} 