<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 03.02.14
 * Time: 15:26
 */

namespace App\TaskBundle\Form;

use App\ProjectBundle\Entity\ProjectRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TasksFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ratioArr = array();
        for ($i = 0; $i <= 10; $i++) {
            $val = ($i>0 ? $i.'0' : '0');
            $ratioArr[$val]= $val.' %';
        }

        $builder->add('project', 'entity_search', [
            'label' => 'project',
            'translation_domain' => 'Project',
            'entity_options' => [
                'class' => 'App\ProjectBundle\Entity\Project',
                'query_builder' => function(ProjectRepository $repository){
                    return $repository->getDefaultQueryBuilder()->orderBy($repository->column('name'), 'asc');
                },
                'empty_value' => 'All projects',
            ],
            'required' => false,
        ])->add('tracker', 'entity_search', [
            'label' => 'tracker',
            'translation_domain' => 'Tasks',
            'required' => false,
            'entity_options' => [
                'class' => 'App\TaskBundle\Entity\TaskTracker',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('t')->orderBy('t.name', 'asc');
                }
            ]
        /*
        // this section has to be updated to status bundle
        ])->add('status', 'entity_search', [
            'label' => 'Status',
            'required' => false,
            'entity_options' => [
                'class' => 'App\TaskBundle\Entity\TaskStatus',
                'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('s')->orderBy('s.name', 'asc');
                    }
            ]
        */
        ])->add('priority', 'entity_search', [
            'label' => 'priority',
            'translation_domain' => 'Tasks',
            'required' => false,
            'entity_options' => [
                'class' => 'App\TaskBundle\Entity\TaskPriority',
                'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('p')->orderBy('p.name', 'asc');
                    }
            ]
        ])->add('category', 'entity_search', [
            'label' => 'category',
            'translation_domain' => 'Project',
            'required' => false,
            'entity_options' => [
                'class' => 'App\TaskBundle\Entity\TaskCategory',
                'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('c')->orderBy('c.name', 'asc');
                    }
            ]
        ])->add('assigned_to', 'entity_search', [
            'label' => 'assigned_to',
            'translation_domain' => 'Tasks',
            'required' => false,
            'entity_options' => [
                'class' => 'App\PersonBundle\Entity\Person',
                'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('p')->orderBy('p.lastName', 'asc')->addOrderBy('p.firstName', 'asc');
                    }
            ]
        ])->add('doneRatio', 'number_search', [
            'label' => 'done_ratio',
            'translation_domain' => 'Tasks',
            'choices' => $ratioArr
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'label' => 'add_filter',
            'translation_domain' => 'Common'
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_tasks_filters';
    }

    public function getParent()
    {
        return 'filters';
    }


} 