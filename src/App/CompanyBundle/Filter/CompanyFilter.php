<?php

namespace App\CompanyBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class CompanyFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\CompanyBundle\Entity\Company.id',
        'name'        => 'App\CompanyBundle\Entity\Company.name',
        'companyType' => 'companyType.name',
        'sector'      => 'sector.title',
        'phoneType'   => 'App\CompanyBundle\Entity\Company.id',
        'addresses'   => 'App\CompanyBundle\Entity\Company.id',
        'createdAt'   => 'App\CompanyBundle\Entity\Company.createdAt',
        'updatedAt'   => 'App\CompanyBundle\Entity\Company.updatedAt',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:companyTypeFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:sectorFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:phonesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:addressesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:createdAtFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppCompanyBundle:DtModelStyle/Company:updatedAtFormatter.html.twig', ['entity' => $entity,]);
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

//        $criteria['limit'] = $this->itemsPerPage;

        $qb = $this->getEntityManager()->getRepository('AppCompanyBundle:Company')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['companyType'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['sector'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['phoneType'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['addresses'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['createdAt'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['updatedAt'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}