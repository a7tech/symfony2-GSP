<?php

namespace App\IndustryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SpecialityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('sector', null, array('required'=>true, 'empty_value'=>'Choose an option', 'attr' => array('class' => 'form-control')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\IndustryBundle\Entity\Speciality'
        ));
    }

    public function getName()
    {
        return 'app_industrybundle_specialitytype';
    }
}
