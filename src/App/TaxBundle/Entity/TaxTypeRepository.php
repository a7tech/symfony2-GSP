<?php

namespace App\TaxBundle\Entity;

use App\CoreBundle\Entity\EntityRepository;

class TaxTypeRepository extends EntityRepository
{

    public function getQueryBuilderByCriteria(array $criteria = array())
    {
        $qb = parent::getQueryBuilderByCriteria($criteria);

        return $qb
                        ->leftJoin($this->column('country'), 'country')
        ;
    }

}