<?php

namespace App\AddressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('country',null, array('empty_value' => 'Choose an option', 'attr' => array('class' => 'form-control')))
            ->add('province', null, array('empty_value' => 'Choose an option', 'attr' => array('class' => 'form-control')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AddressBundle\Entity\Region'
        ));
    }

    public function getName()
    {
        return 'app_addressbundle_regiontype';
    }
}
