<?php

namespace App\UserBundle\Form;

use App\PersonBundle\Entity\PersonRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $form = $event->getForm();
            $user = $event->getData();

            $form->add('person', 'entity', [
                'required' => true,
                'empty_value' => false,
                'class' => 'App\PersonBundle\Entity\Person',
                'query_builder' => function(PersonRepository $repository) use ($user){
                    return $repository->getNotAssignedPersonsQueryBuilder($user->getId() !== null ? $user : null);
                }
            ]);
        })
        ->add('email', 'email', array('required'=>true, 'by_reference'=>false))
        ->add('enabled', null, array('required'=>false))
        ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();

            $form->add('plainPassword','repeated' ,
                array(
                    'required'=> $data->getId() === null,
                    'type' => 'password',
                    'first_options' => array('label'=>'Password'),
                    'second_options' => array('label'=>'Confirm Password'),
                ));
        })
        ->add('groups');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'app_usertype';
    }


}
