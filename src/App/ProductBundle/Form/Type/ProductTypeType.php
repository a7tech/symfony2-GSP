<?php
/**
 * ProductTypeType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 07.08.13 21:50
 */

namespace App\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class ProductTypeType extends AbstractType
{
    public function getName()
    {
        return 'product_type';
    }

    public function getParent()
    {
        return 'entity';
    }
}