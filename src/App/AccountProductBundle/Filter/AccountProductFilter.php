<?php

namespace App\AccountProductBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class AccountProductFilter extends BaseFilter
{

    protected $columns = [
        'id'                => 'App\AccountProductBundle\Entity\AccountProduct.id',
        'image'             => 'App\AccountProductBundle\Entity\AccountProduct.id',
        'productTitle'      => 'product.title',
        'productCode'       => 'product.productCode',
        'productbrandGroup' => 'brandGroup.id',
        'priceList'         => 'App\AccountProductBundle\Entity\AccountProduct.id',
        'priceSell'         => 'App\AccountProductBundle\Entity\AccountProduct.id',
        'productCategories' => 'App\AccountProductBundle\Entity\AccountProduct.id',
        'suppliers'         => 'App\AccountProductBundle\Entity\AccountProduct.id',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:imageFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:productTitleFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:productCodeFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:brandFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:priceListFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:priceSellFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:productCategoriesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppAccountProductBundle:DtModelStyle/AccountProduct:suppliersFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppAccountProductBundle:AccountProduct')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['image'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['productTitle'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['productCode'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['productbrandGroup'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['priceList'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['priceSell'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['productCategories'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['suppliers'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}