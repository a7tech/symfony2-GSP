<?php

namespace App\ProductBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class BrandGroupFilter extends BaseFilter
{

    protected $columns = [
        'no'            => 'App\ProductBundle\Entity\BrandGroup.id',
        'id'            => 'App\ProductBundle\Entity\BrandGroup.id',
        'name'          => 'App\ProductBundle\Entity\BrandGroup.title',
        'purchaseCoeff' => 'App\ProductBundle\Entity\BrandGroup.purchaseCoeff',
        'resellCoeff'   => 'App\ProductBundle\Entity\BrandGroup.resellCoeff',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/BrandGroup:noFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/BrandGroup:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/BrandGroup:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/BrandGroup:purchaseCoeffFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/BrandGroup:resellCoeffFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppProductBundle:BrandGroup')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['no'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['purchaseCoeff'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['resellCoeff'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}