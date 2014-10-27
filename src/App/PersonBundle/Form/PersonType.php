<?php

namespace App\PersonBundle\Form;

use App\AddressBundle\Form\AdressesType;
use App\ProductBundle\Form\FormMapper;
use App\UserBundle\Form\UserType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;

class PersonType extends AbstractType
{

    public  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mapper = new FormMapper($builder);
        $mapper
            ->with('Contact details')
            ->add('image', 'person_image_form', array(
                'label' => 'Contact image',
                'embed_form' => true,
                'required' => false
            ))
            ->add('firstName', null, array('label'=>'First Name'))
            ->add('lastName', null, array('label'=>'Last Name'))
            ->add('gender', 'choice', array(
                'choices'=> array('m'=>'Male', 'f'=>'Femaile'),
                'empty_value'=>'Choose an option',
                'preferred_choices'=>array('empty_value'),
                'attr' => array('class' => 'form-control'),
            ))
            ->add('birthDate', 'datepicker', array(
                'label' => 'Birth Date',
                'required' => false
            ))
            ->add('emails', 'collection', array(
                'type' => new \App\EmailBundle\Form\EmailType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => true,
                'attr'      => array('class' => 'horizontal-form')))
            ->add('socialMedias', 'collection', array(
                'type' => new \App\SocialMediaBundle\Form\SocialMediaType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'horizontal-form'),
                'label'=>'Social Medias'))
            ->add('phones', 'collection', array(
                'type' => new \App\PhoneBundle\Form\PhoneType($this->em),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'horizontal-form')
                ))
            ->add('addresses', 'backend_addresses')
            ->add('employments', 'collection', array(
                'type' => new \App\EmploymentBundle\Form\EmploymentPersonType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'horizontal-form')))
            ->add('personGroup', 'entity', array(
                'class'=>'AppPersonBundle:PersonGroup',
                'label'=>'Contact Group',
                'required' => false,
                'empty_value'=>'Choose an option',
                'multiple'=>true,
                'expanded'=>true,
                'attr' => array('class' => 'checkbox'),
                'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }
            ))
            ->with('Employments')
                ->add('employments', 'collection', array(
                    'type' => 'employment_person_form',
                    'allow_add'=>true,
                    'allow_delete'=>true,
                    'required'  => false,
                    'attr'      => array('class' => 'horizontal-form')))
            ->with('Skills')
                ->add('skills', 'collection', array(
                    'type' => 'skill_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                    'attr' => array('class' => 'horizontal-form')
                ))
            ->with('CV')
                ->add('educations', 'collection', array(
                    'type' => 'education_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('languages', 'collection', array(
                    'type' => 'lang_experience_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('certifications', 'collection', array(
                    'type' => 'certification_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('workExperiences', 'collection', array(
                    'type' => 'work_experience_form',
                    'allow_add' => true,
                    'label' => 'Work Experience',
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('associations', 'collection', array(
                    'type' => 'association_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('militaryHistories', 'collection', array(
                    'label' => 'Military History',
                    'type' => 'military_history_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('references', 'collection', array(
                    'type' => 'reference_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
                ->add('objective', 'tinymce', array(
                    'label' => 'Career Objective',
                    'required' => false
                ))
            ->with('HR')
                ->add('hrInfo', 'hr_info_form', array(
                    'label' => 'HR Information',
                    'embed_form' => true,
                    'required' => false
                ))
                ->add('salaryInfo', 'salary_info_form', array(
                    'label' => 'Salary Information',
                    'embed_form' => true,
                    'required' => false,
                    'attr'=>array('class'=>'collection_row')
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PersonBundle\Entity\Person'
        ));
    }

    public function getName()
    {
        return 'person_form';
    }
}
