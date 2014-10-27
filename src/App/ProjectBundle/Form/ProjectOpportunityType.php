<?php

namespace App\ProjectBundle\Form;

use App\ProjectBundle\Entity\ProjectRepository;
use App\IndustryBundle\Entity\Sector;
use App\PersonBundle\Form\PersonType;
use App\ProductBundle\Form\FormMapper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ProjectOpportunityType extends AbstractType
{
    public   $em;
    public   $sector;

    public function __construct(EntityManager $em, Sector $sector=null)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $preferedIsoChoices = $this->em->getRepository('AppCurrencyBundle:Currency')->getPreferredCurrency();

        $builder->add('name', null, array(
                    'label' => 'name',
                    'required' => true,
                    'translation_domain' => 'Common'))
                ->add('accountProfile', 'entity', array(      
                    'required'  => true,
                    'class' => 'App\AccountBundle\Entity\AccountProfile',
                    'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('a')->orderBy('a.name', 'ASC');
                    },
                    'attr'=>array('class'=>'form-control'),
                    'property'  => 'name',
                    'empty_value' => 'Choose an option',
                    'label'     => 'vendor_company',
                    'translation_domain' => 'AccountProfile'
                    ))
                ->add('expectedDate', 'date', array(
                    'label' => 'opportunity.expected_close_date',
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr' => array('class' => 'datetimepicker')))
                ->add('description', 'textarea', array(
                    'label' => 'opportunity.description',
                    'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                    'required' => false))
                
                ->add('milestone', 'entity', array(      
                    'required'  => true,
                    'class' => 'App\ProjectBundle\Entity\ProjectMilestone',
                    'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('m')->orderBy('m.value', 'ASC');
                    },
                    'attr'=>array('class'=>'form-control'),
                    'property'  => 'name',
                    'label'     => 'milestone'
                    )
                )
                ->add('expectedValue', 'number', array(
                    'label' => 'opportunity.expected_value'))
                ->add('currency', 'entity', array(
                    'class'=>'AppCurrencyBundle:Currency',
                    'expanded'=>false,
                    'multiple'=>false,
                    'required'  => true,
                    'attr'=>array('class'=>'form-control'),
                    'query_builder'=>function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('sp')->orderBy('sp.name', 'ASC');
                    },
                    'preferred_choices'=>$preferedIsoChoices,
                    'label' => 'currency',
                    'translation_domain' => 'Currency'
                ))

                ->add('owner', 'entity', [
                    'label' => 'owner',
                    'required' => false,
                    'select2' => true,
                    'empty_value'=>'Choose an option',
                    'class' => 'App\UserBundle\Entity\User',
                    'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('u')->orderBy('u.username');
                    }
                ])

                ->add('commision', 'number', array(
                    'label' => 'opportunity.commision'))
                ->add('client', 'person_autocomplete', array(
                    'required'=>true,
                    'label' => 'client'))
                ->add('progress', 'percent', [
                    'label' => 'opportunity.progress',
                    'required' => true,
                    'type' => 'fractional'])
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProjectBundle\Entity\ProjectOpportunity',
            'translation_domain' => 'Project'
        ));
    }

    public function getName()
    {
        return 'project_opportunity_form';
    }
}