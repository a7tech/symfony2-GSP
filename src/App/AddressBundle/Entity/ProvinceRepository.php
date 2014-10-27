<?php

/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/18/13
 * Time: 8:16 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\AddressBundle\Entity;

use App\CoreBundle\Entity\EntityRepository;

class ProvinceRepository extends EntityRepository
{

    /**
     * Get Province lis by Countries
     * @param string $countryId
     */
    public function getProvincesByCountries($countryId, $asArray = true) {
        $em = $this->_em;
        $query = $em->createQuery('SELECT u FROM App\AddressBundle\Entity\Province u
                                    JOIN u.country a
                                    WHERE a.id = ' . $countryId . '
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