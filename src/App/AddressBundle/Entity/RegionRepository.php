<?php

/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/28/13
 * Time: 2:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\AddressBundle\Entity;

use App\CoreBundle\Entity\EntityRepository;

class RegionRepository extends EntityRepository
{

    public function getRegionsByProvince($provinceId, $asArray = true) {
        $em = $this->_em;
        $query = $em->createQuery('SELECT u FROM App\AddressBundle\Entity\Region u
                                    JOIN u.province a
                                    WHERE a.id = ' . $provinceId . '
                                    ORDER BY u.name ASC');
        $res   = $query->getResult();

        if($asArray){
            $provinces = array();

            foreach ($res as $result) {
                $buff = array();
                $buff['id'] = $result->getId();
                $buff['name'] = $result->getName();
                $provinces[] = $buff;
            }
            return $provinces;
        } else {
            return $res;
        }
    }

}