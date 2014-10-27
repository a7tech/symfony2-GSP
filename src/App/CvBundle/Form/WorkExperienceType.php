<?php
/**
 * WorkExperienceType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 13:33
 */

namespace App\CvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employer', null, array(
                'required' => true,
                'label' => 'Employer'
            ))
            ->add('title', null, array(
                'required' => true,
                'label' => 'Job Title'
            ))
            ->add('jobFunction', null, array(
                'required' => true,
                'label' => 'Job Function'
            ))
            ->add('description', 'tinymce', array(
                'label' => 'Description',
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
                'label' => 'My current position',
                'required' => false,
            ))
            ->add('managerName', null, array(
                'label' => 'Manager Name',
                'required' => false
            ))
            ->add('managerEmail', 'email', array(
                'label' => 'Manager Email',
                'required' => false
            ))
            ->add('managerWorkPhone', null, array(
                'label' => 'Manager Work Phone',
                'required' => false
            ))
            ->add('managerMobilePhone', null, array(
                'label' => 'Manager Mobile Phone',
                'required' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CvBundle\Entity\WorkExperience'
        ));
    }

    public function getName()
    {
        return 'work_experience_form';
    }
}