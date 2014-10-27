<?php

namespace App\AddressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,array('label'=>'Name'))
            ->add('alterName', null,array('label'=>'Alternative Name', 'required'=>false))
            ->add('twoCharCode', null,array('label'=>'2 Char Code', 'required'=>false))
            ->add('threeCharCode', null,array('label'=>'3 Char Code', 'required'=>false))
            ->add('numberCode', null,array('label'=>'Number Code', 'required'=>false))
            ->add('fipsCountryCode', null,array('label'=>'FISP Country Code', 'required'=>false))
            ->add('fipsCountryName', null,array('label'=>'FISP Country Name', 'required'=>false))
            ->add('cdhId', null,array('label'=>'CDHID', 'required'=>false))
            ->add('lat', null,array('label'=>'Latitude', 'required'=>false))
            ->add('long', null,array('label'=>'Longitude', 'required'=>false))
            ->add('timezones', null,array('label'=>'Time zones', 'required'=>false))
            ->add('isoCode', null,array('label'=>'Phone Iso Code', 'required'=>false,'attr' => array('class' => 'form-control')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AddressBundle\Entity\Country'
        ));
    }

    public function getName()
    {
        return 'app_addressbundle_countrytype';
    }
}
