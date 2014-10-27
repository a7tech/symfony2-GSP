<?php

namespace App\UserBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class UserFilter extends BaseFilter
{

    protected $columns = [
        'id'              => 'App\UserBundle\Entity\User.id',
        'personFirstName' => 'person.firstName',
        'email'           => 'App\UserBundle\Entity\User.email',
        'enabled'         => 'App\UserBundle\Entity\User.enabled',
        'lastLogin'       => 'App\UserBundle\Entity\User.lastLogin',
        'locked'          => 'App\UserBundle\Entity\User.locked',
        'expired'         => 'App\UserBundle\Entity\User.expired',
        'groups'          => 'App\UserBundle\Entity\User.id',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:personFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:emailFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:enabledFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:lastLoginFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:lockedFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:expiredFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppUserBundle:DtModelStyle/User:groupsFormatter.html.twig', ['entity' => $entity,]);
            $results[$count]['DT_RowId'] = $entity->getId();
            $count += 1;
        }

        return [
            'aaData'               => $results,
            "iTotalRecords"        => $countRows,
            "iTotalDisplayRecords" => $countRows,
        ];
    }

    /**
     * getQueryBuilder
     *
     * @param Request $request
     *
     * @return null
     */
    public function getQueryBuilder(Request $request)
    {
        $criteria                    = $request->query->all();
        $criteria['isAlreadySorted'] = false;

        if ($this->container->hasParameter('itemsPerPage')) {
            $this->itemsPerPage = $this->container->getParameter('itemsPerPage');
        }

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $criteria['isAlreadySorted'] = true;
        }

        $qb = $this->getEntityManager()->getRepository('AppUserBundle:User')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['personFirstName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['email'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['enabled'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['lastLogin'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['locked'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['expired'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['groups'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}