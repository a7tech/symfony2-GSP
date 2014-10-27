<?php
/**
 * SuppliersType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 09.08.13 14:28
 */

namespace App\CompanyBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanySingleAutocompleteType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array( 'required'=>true,
            'label' => 'company',
            'translation_domain' => 'Company',
            'class' => 'App\CompanyBundle\Entity\CommonCompany',
            'multiple' => false,
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('n')->orderBy('n.name', 'ASC');
            }
        ));
    }

    public function getName()
    {
        return 'company_single_autocomplete';
    }

    public function getParent()
    {
        return 'entity';
    }
}