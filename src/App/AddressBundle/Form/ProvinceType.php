<?php

namespace App\AddressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProvinceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', null, array('label'=>'Name'))
            ->add('alterName', null, array('label'=>'Alternative Name'))
            ->add('levelName', null, array('label'=>'Level Name'))
            ->add('isoCode', null, array('label'=>'Char Code'))
            ->add('cdhId', null, array('label'=>'CDHID'))
            ->add('country', null, array('label'=>'Country', 'attr' => array('class' => 'form-control')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AddressBundle\Entity\Province'
        ));
    }

    public function getName()
    {
        return 'app_addressbundle_provincetype';
    }
}
