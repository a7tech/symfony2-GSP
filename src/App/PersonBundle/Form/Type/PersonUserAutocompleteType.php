<?php
/**
 * SuppliersType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 09.08.13 14:28
 */

namespace App\PersonBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonUserAutocompleteType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required'=>true,
            'class' => 'App\PersonBundle\Entity\Person',
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('n')->leftJoin('n.user', 'u')->orderBy('n.firstName', 'ASC');
            }
        ));
    }

    public function getName()
    {
        return 'person_autocomplete';
    }

    public function getParent()
    {
        return 'entity';
    }
}