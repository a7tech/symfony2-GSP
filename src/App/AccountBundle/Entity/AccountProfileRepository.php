<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 9/2/13
 * Time: 10:01 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\AccountBundle\Entity;


use App\CoreBundle\Entity\EntityRepository;

class AccountProfileRepository extends EntityRepository
{
    public function getDefaultQueryBuilder()
    {
        $query_builder = parent::getDefaultQueryBuilder();
        $query_builder->orderBy($this->column('name'), 'ASC');

        return $query_builder;
    }

}