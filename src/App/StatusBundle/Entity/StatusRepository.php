<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.01.14
 * Time: 11:30
 */

namespace App\StatusBundle\Entity;


use App\CoreBundle\Entity\EntityRepository;

class StatusRepository extends EntityRepository
{
    public function getDefaultQueryBuilder()
    {
        $query_builder = parent::getDefaultQueryBuilder();
        $query_builder->orderBy($this->column('order'));

        return $query_builder;
    }
} 