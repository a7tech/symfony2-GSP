<?php

namespace App\AccountBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class AccountProfileFilter extends BaseFilter
{

    protected $columns = [
        'id'           => 'App\AccountBundle\Entity\AccountProfile.id',
        'name'         => 'App\AccountBundle\Entity\AccountProfile.name',
        'creationDate' => 'App\AccountBundle\Entity\AccountProfile.creationDate',
        'createdAt'    => 'App\AccountBundle\Entity\AccountProfile.createdAt',
        'updatedAt'    => 'App\AccountBundle\Entity\AccountProfile.updatedAt',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppAccountBundle:DtModelStyle/AccountProfile:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountBundle:DtModelStyle/AccountProfile:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountBundle:DtModelStyle/AccountProfile:creationDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountBundle:DtModelStyle/AccountProfile:createdAtFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountBundle:DtModelStyle/AccountProfile:updatedAtFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppAccountBundle:AccountProfile')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['creationDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['createdAt'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['updatedAt'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}