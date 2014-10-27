<?php
/**
 * CategoryTreeType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 10.07.13 20:52
 */

namespace App\CategoryBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryTreeType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'expanded' => true,
            'multiple' => true,
            'property' => 'formField',
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('n')->orderBy('n.lft', 'ASC');
            }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'category_tree';
    }
}