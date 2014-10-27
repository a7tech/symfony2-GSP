<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 31.01.14
 * Time: 14:41
 */

namespace App\CalendarBundle\Form;

use App\AccountBundle\Entity\AccountProfileRepository;
use App\ProjectBundle\Entity\ProjectRepository;
use App\UserBundle\Entity\UserRepository;
use PhpOption\Tests\Repository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', 'entity', [
            'label' => 'Events',
            'class' => 'App\UserBundle\Entity\User',
            'query_builder' => function(UserRepository $repository){
                    return $repository->getDefaultQueryBuilder()->orderBy($repository->column('username'));
                },
            'empty_value' => 'My events',
            'required' => false
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_calendar_filter';
    }

    public function getParent()
    {
        return 'backend_tasks_filters';
    }
} 