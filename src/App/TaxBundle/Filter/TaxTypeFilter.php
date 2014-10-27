<?php

namespace App\TaxBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class TaxTypeFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\TaxBundle\Entity\TaxType.id',
        'name'        => 'App\TaxBundle\Entity\TaxType.name',
        'rate'        => 'App\TaxBundle\Entity\TaxType.rate',
        'country'     => 'country.name',
        'description' => 'App\TaxBundle\Entity\TaxType.description',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppTaxBundle:DtModelStyle/TaxType:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaxBundle:DtModelStyle/TaxType:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaxBundle:DtModelStyle/TaxType:rateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaxBundle:DtModelStyle/TaxType:countryFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaxBundle:DtModelStyle/TaxType:descriptionFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppTaxBundle:TaxType')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['rate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['country'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['description'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}