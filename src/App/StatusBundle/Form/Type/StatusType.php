<?php

namespace App\StatusBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('originalName', 'text', [
                'read_only' => true
            ])
            ->add('description')
            ->add('color')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\\StatusBundle\\Entity\\Status'
        ));
    }

    public function getName()
    {
        return 'app_status_status';
    }
}
