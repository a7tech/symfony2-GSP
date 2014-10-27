<?php

namespace App\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name')
            ->add('creationDate', 'date',
                array(
                    'attr'=> array(
                        'class' => 'datepicker',
                    ),
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'label'=>'Creation Date'))
            ->add('description')
            ->add('emails', 'collection', array(
                'type' => new \App\EmailBundle\Form\EmailType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'email-box')))
            ->add('socialMedias', 'collection', array(
                'type' => new \App\SocialMediaBundle\Form\SocialMediaType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'email-box'),
                'label'=>'Social Medias'))
            ->add('phones', 'collection', array(
                'type' => new \App\PhoneBundle\Form\PhoneType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'email-box')))
            ->add('addresses', 'addresses_backend', array(
                'attr'      => array('class' => 'email-box')))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AccountBundle\Entity\Profile'
        ));
    }

    public function getName()
    {
        return 'app_accountbundle_profiletype';
    }
}
