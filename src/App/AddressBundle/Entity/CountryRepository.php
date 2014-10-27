<?php

/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/11/13
 * Time: 11:53 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\AddressBundle\Entity;

use App\CoreBundle\Entity\EntityRepository;

class CountryRepository extends EntityRepository
{

    public function getQueryBuilderByCriteria(array $criteria = array())
    {
        $qb = parent::getQueryBuilderByCriteria($criteria);

        return $qb
                        ->leftJoin($this->column('isoCode'), 'isoCode')
        ;
    }

}