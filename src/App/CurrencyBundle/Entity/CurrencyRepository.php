<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 06.03.14
 * Time: 15:16
 */

namespace App\CurrencyBundle\Entity;


use App\CoreBundle\Entity\EntityRepository;

class CurrencyRepository extends EntityRepository
{
    public function getPreferredCurrency()
    {
        $qb = $this->createQueryBuilder('c');

        $qb->where('c.code = :code1')
            ->orWhere('c.code = :code2')
            ->orWhere('c.code = :code3')
            ->setParameter('code1', "CAD")
            ->setParameter('code2', "USD")
            ->setParameter('code3', "EUR")
        ;

        $query = $qb->getQuery();
        $res = $query->getResult();

        return $res;
    }
} 