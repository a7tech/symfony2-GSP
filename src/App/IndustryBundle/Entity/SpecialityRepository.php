<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/28/13
 * Time: 2:52 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\IndustryBundle\Entity;


use App\CoreBundle\Entity\EntityRepository;

class SpecialityRepository extends EntityRepository {

    public function getSpecialityBySector($provinceId) {
        $em = $this->_em;
        $query = $em->createQuery('SELECT u FROM App\IndustryBundle\Entity\Speciality u
                                    JOIN u.sector a
                                    WHERE a.id = '.$provinceId.'
                                    ORDER BY u.title ASC');
        $res = $query->getResult();

        $provinces = array();

        foreach ($res as $result) {
            $buff = array();
            $buff['id'] = $result->getId();
            $buff['name'] = $result->getTitle();
            $provinces[] = $buff;
        }
        return $provinces;
    }
}