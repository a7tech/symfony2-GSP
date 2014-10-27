<?php
/**
 * EntitySortedType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 23.08.13 16:00
 */

namespace App\FormBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntitySortedType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'query_builder' => function(EntityRepository $repository) {
                $methods = get_class_methods($repository->getClassName());
                if (in_array('getName', $methods)) {
                    $sortField = 'name';
                }
                elseif (in_array('getTitle', $methods)) {
                    $sortField = 'title';
                }
                $qb = $repository->createQueryBuilder('n');
                if (!empty($sortField)) {
                    $qb->orderBy('n.'.$sortField, 'ASC');
                }
                return $qb;
            }
        ));
    }
    
    public function getName()
    {
        return 'entity_sorted';
    }

    public function getParent()
    {
        return 'entity';
    }
}