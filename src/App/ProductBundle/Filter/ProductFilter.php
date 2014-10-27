<?php

namespace App\ProductBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class ProductFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\ProductBundle\Entity\Product.id',
        'image'       => 'App\ProductBundle\Entity\Product.id',
        'title'       => 'App\ProductBundle\Entity\Product.title',
        'productCode' => 'App\ProductBundle\Entity\Product.productCode',
        'brand'       => 'App\ProductBundle\Entity\Product.id',
        'priceList'   => 'App\ProductBundle\Entity\Product.id',
        'priceSell'   => 'App\ProductBundle\Entity\Product.id',
        'categories'  => 'App\ProductBundle\Entity\Product.id',
        'profile'     => 'App\ProductBundle\Entity\Product.id',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:noFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:titleFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:productCodeFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:brandGroupFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:priceListFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:priceSellFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:categoriesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProductBundle:DtModelStyle/Product:noFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppProductBundle:Product')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['image'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['title'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['productCode'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['brand'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['priceList'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['priceSell'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['categories'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['profile'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}