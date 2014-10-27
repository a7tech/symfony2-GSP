<?php
/**
 * EducationType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 12:47
 */

namespace App\CvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EducationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institution', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Institution',
                'required' => false
            ))
            ->add('degree', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Degree',
                 'required' => false
            ))
            ->add('educationLevel', 'choice', array(
                'choices' => array(
                    'None' => 'None',
                    'Hight School Diploma/GED' => 'Hight School Diploma/GED',
                    'Technical Dimploma' => 'Technical Dimploma',
                    "Associate's Degree/College Diploma" => "Associate's Degree/College Diploma",
                    'Non-Degree Program' => 'Non-Degree Program',
                    "Bachelor's Degree" => "Bachelor's Degree",
                    "Master's Degree" => "Master's Degree",
                    'Doctorate Degree' => 'Doctorate Degree',
                    'Higher Degree' => 'Higher Degree',
                    'Other' => 'Other'
                ),
                'label' => 'Education Level',
                'required' => false,
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'preferred_choices' => array('empty_value')
            ))
            ->add('gpa', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'GPA', 'required' => false
            ))
            ->add('description', 'tinymce', array(
                'label' => 'Other information',
                'required' => false
            ))
            ->add('startDate', 'datepicker', array(
                'required' => true,
                'label' => 'Start Date',
            ))
            ->add('endDate', 'datepicker', array(
                'required' => false,
                'label' => 'End Date',
            ))
            ->add('current', 'checkbox', array(
                'label' => 'Diploma in progress',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CvBundle\Entity\Education'
        ));
    }

    public function getName()
    {
        return 'education_form';
    }
}