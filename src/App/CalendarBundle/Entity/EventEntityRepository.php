<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 31.01.14
 * Time: 18:02
 */

namespace App\CalendarBundle\Entity;


use App\CoreBundle\Entity\EntityRepository;
use App\UserBundle\Entity\User;

class EventEntityRepository extends EntityRepository
{
    /**
     * Gets tasks in range
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param \App\UserBundle\Entity\User $user
     * @return array
     */
    public function getInRange(\DateTime $startDate, \DateTime $endDate, User $user = null)
    {
        $startDateColumn = $this->column('startDatetime');
        $endDateColumn = $this->column('endDatetime');

        $query_builder = $this->getDefaultQueryBuilder();
        $query_builder->andWhere($query_builder->expr()
                    ->orX($startDateColumn.' >= :startDate AND '.$startDateColumn.' <= :endDate')
                    ->add($endDateColumn.' >= :startDate AND '.$endDateColumn.' <= :endDate')
            )
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        if($user !== null){
            $query_builder->andWhere($this->column('user').' = :user')
                ->setParameter('user', $user);
        }

        return $query_builder->getQuery()->getResult();
    }
} 