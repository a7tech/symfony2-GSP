<?php

namespace App\PurchaseBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class PurchaseFilter extends BaseFilter
{

    protected $columns = [
        'id'             => 'App\PurchaseBundle\Entity\Purchase.id',
        'accountProfile' => 'accountProfile.name',
        'supplier'       => 'supplier.name',
        'creationDate'   => 'App\PurchaseBundle\Entity\Purchase.creationDate',
        'editDate'       => 'App\PurchaseBundle\Entity\Purchase.editDate',
        'sentDate'       => 'App\PurchaseBundle\Entity\Purchase.sentDate',
        'invoiceDate'    => 'App\PurchaseBundle\Entity\Purchase.invoiceDate',
        'isDraft'        => 'App\PurchaseBundle\Entity\Purchase.isDraft',
        'status'         => 'App\PurchaseBundle\Entity\Purchase.status',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:accountProfileFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:supplierFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:creationDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:editDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:sentDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:invoiceDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:isDraftFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPurchaseBundle:DtModelStyle/Purchase:statusFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppPurchaseBundle:Purchase')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['accountProfile'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['supplier'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['creationDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['editDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['sentDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['invoiceDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['isDraft'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['status'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}