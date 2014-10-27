<?php

namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\UserBundle\Entity\RoleRepository as EntityRepository;

class PermissionsGroupType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('roles','entity', array(
                'class' => 'AppUserBundle:Role',
                'expanded' => true,
                'multiple' => true,
                'property' => 'formdescription',
                'query_builder' => function(EntityRepository $er) {
                        $qb = $er->createQueryBuilder('u');
                        $qb
                          ->orderBy('u.module, u.role', 'ASC')
                          ;
                        return $qb;
                    },
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\UserBundle\Entity\PermissionsGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_userbundle_permissionsgroup';
    }
}
