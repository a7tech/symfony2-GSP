<?php

namespace App\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'vlabs_file', array(
                'label' => 'file.image',
                'translation_domain' => 'Common'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TaskBundle\Entity\TaskFile'
        ));
    }

    public function getName()
    {
        return 'app_taskbundle_taskfiletype';
    }
}