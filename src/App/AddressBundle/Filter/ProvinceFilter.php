<?php

namespace App\AddressBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class ProvinceFilter extends BaseFilter
{

    protected $columns = [
        'id'        => 'App\AddressBundle\Entity\Province.id',
        'name'      => 'App\AddressBundle\Entity\Province.name',
        'country'   => 'App\AddressBundle\Entity\Province.country',
        'alterName' => 'App\AddressBundle\Entity\Province.alterName',
        'levelName' => 'App\AddressBundle\Entity\Province.levelName',
        'regions'   => 'App\AddressBundle\Entity\Province.id',
        'isoCode'   => 'App\AddressBundle\Entity\Province.isoCode',
        'cdhId'     => 'App\AddressBundle\Entity\Province.cdhId',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:countryFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:alterNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:levelNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:regionsFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:isoCodeFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Province:cdhIdFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppAddressBundle:Province')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['country'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['alterName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['levelName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['regions'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['isoCode'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['cdhId'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}