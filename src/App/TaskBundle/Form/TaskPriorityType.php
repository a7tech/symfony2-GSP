<?php

namespace App\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskPriorityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'name',
                'translation_domain' => 'Common'
            ])
            ->add('description', null, [
                'label' => 'description',
                'translation_domain' => 'Common'
            ])
            ->add('color', null, [
                'label' => 'color',
                'translation_domain' => 'Status'
            ])
            ->add('value', null, [
                'label' => 'value',
                'translation_domain' => 'Status'
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TaskBundle\Entity\TaskPriority',
            'translation_domain' => 'Tasks'
        ));
    }

    public function getName()
    {
        return 'app_taskbundle_taskprioritytype';
    }
}