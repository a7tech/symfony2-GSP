<?php

namespace App\AddressBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class CountryFilter extends BaseFilter
{

    protected $columns = [
        'id'            => 'App\AddressBundle\Entity\Country.id',
        'name'          => 'App\AddressBundle\Entity\Country.name',
        'alterName'     => 'App\AddressBundle\Entity\Country.alterName',
        'twoCharCode'   => 'App\AddressBundle\Entity\Country.twoCharCode',
        'threeCharCode' => 'App\AddressBundle\Entity\Country.threeCharCode',
        'provinces'     => 'App\AddressBundle\Entity\Country.id',
        'isoCode'       => 'isoCode.prefix',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:alterNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:twoCharCodeFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:threeCharCodeFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:provincesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAddressBundle:DtModelStyle/Country:isoCodeFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppAddressBundle:Country')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['alterName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['twoCharCode'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['threeCharCode'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['provinces'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['isoCode'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}