<?php

namespace App\PlaceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlaceType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'name'
            ])
            ->add('description', null, [
                'label' => 'description'
            ])
            ->add('importance', null, [
                'label' => 'importance',
                'translation_domain' => 'Place'
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PlaceBundle\Entity\Place',
            'translation_domain' => 'Common'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'place';
    }
}
