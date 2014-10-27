<?php

namespace App\LanguageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LanguageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso', null, array('label' => 'Add Language'))
            ->add('file', null, array('label' => 'Flag'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\LanguageBundle\Entity\Language'
        ));
    }

    public function getName()
    {
        return 'app_userbundle_languagetype';
    }
}
