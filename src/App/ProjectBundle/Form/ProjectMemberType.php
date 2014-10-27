<?php

namespace App\ProjectBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('members', 'entity', array_merge([
                'required' => false,
                'multiple' => true,
                'select2' => true,
                'label' => 'participants',
                 ],[
                        'class' => 'App\UserBundle\Entity\User',
                        'query_builder' => function(EntityRepository $repository){
                            return $repository->getDefaultQueryBuilder();
                        }
                    ]))
            ->add('client', 'entity', [
                'label' => 'client',
                'required' => false,
                'empty_value'=>'Choose an option',
                'class' => 'App\UserBundle\Entity\User',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('u')->orderBy('u.username');
                }
            ])
            ->add('manager', 'entity', [
                'label' => 'manager',
                'required' => false,
                'empty_value'=>'Choose an option',
                'class' => 'App\UserBundle\Entity\User',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('u')->orderBy('u.username');
                }
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProjectBundle\Entity\Project',
            'translation_domain' => 'Project'
        ));
    }

    public function getName()
    {
        return 'project_member_form';
    }
}
