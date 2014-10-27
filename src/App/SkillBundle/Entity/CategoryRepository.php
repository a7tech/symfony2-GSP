<?php
/**
 * CategoryRepository
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 21.08.13 0:41
 */

namespace App\SkillBundle\Entity;

use App\CategoryBundle\Entity\CategoryRepository as CommonCategoryRepository;

class CategoryRepository extends CommonCategoryRepository
{
    /**
     * getListBySpecialityId
     *
     * @param int $specialityId
     * @return array
     */
    public function getListBySpecialityId($specialityId)
    {
        $qb = $this->createQueryBuilder('cat')
            ->innerJoin('cat.speciality', 'sp')
            ->where('sp.id = :speciality_id')
            ->setParameter('speciality_id', $specialityId);

        return $qb->getQuery()->getResult();

    }
}