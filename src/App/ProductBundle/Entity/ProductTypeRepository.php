<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 22.01.14
 * Time: 02:35
 */

namespace App\ProductBundle\Entity;


use App\CoreBundle\Entity\EntityRepository;

class ProductTypeRepository extends EntityRepository
{
    public function getDefaultQueryBuilder()
    {
        $query_builder = parent::getDefaultQueryBuilder();
        $query_builder->orderBy($this->column('name'), 'ASC');

        return $query_builder;
    }

} 