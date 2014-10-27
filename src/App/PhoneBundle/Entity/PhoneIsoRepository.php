<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 7/9/13
 * Time: 11:42 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\PhoneBundle\Entity;


use Doctrine\ORM\EntityRepository;

class PhoneIsoRepository extends EntityRepository {

    public function findCanada() {
        $em = $this->_em;
        $query = $em->createQuery('SELECT p FROM App\PhoneBundle\Entity\PhoneIso p
                                    INNER JOIN p.country c
                                    WHERE c.name=\'Canada\'');
        $res = $query->getResult();
        return $res;
    }
}