<?php

/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 31.01.14
 * Time: 20:19
 */

namespace App\UserBundle\Entity;

use App\CoreBundle\Entity\EntityRepository;

class PermissionsGroupRepository extends EntityRepository
{

    public function getQueryBuilderByCriteria(array $criteria = array())
    {
        $qb = parent::getQueryBuilderByCriteria($criteria);

        return $qb
//                        ->leftJoin($this->column('person'), 'person')
        ;
    }

}