<?php

namespace App\InvoiceBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;
use App\StatusBundle\Utils\StatusTranslator;
use App\InvoiceBundle\Entity\SaleOrder;

class SaleOrderFilter extends BaseFilter
{

    protected $columns = [
        'id'                  => 'App\InvoiceBundle\Entity\SaleOrder.id',
        'isDraft'             => 'App\InvoiceBundle\Entity\SaleOrder.isDraft',
        'relationName'        => 'App\InvoiceBundle\Entity\SaleOrder.depositPosition',
        'customerName'        => 'App\InvoiceBundle\Entity\SaleOrder.customerName',
        'customerCompanyName' => 'customerCompany.name',
        'vendorName'          => 'vendor.firstName',
        'vendorCompanyName'   => 'vendorCompany.name',
        'currency'            => 'App\InvoiceBundle\Entity\SaleOrder.currency',
        'total'               => 'App\InvoiceBundle\Entity\SaleOrder.id',
        'paid'                => 'App\InvoiceBundle\Entity\SaleOrder.id',
        'due'                 => 'App\InvoiceBundle\Entity\SaleOrder.id',
        'invoiceDate'         => 'App\InvoiceBundle\Entity\SaleOrder.invoiceDate',
        'status'              => 'App\InvoiceBundle\Entity\SaleOrder.status',
    ];
    protected $statusTranslator;

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];
        $statuses = $this->statusTranslator->getStatuses(SaleOrder::STATUSES_GROUP);

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:draftFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:relationNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:customerNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:customerCompanyNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:vendorNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:vendorCompanyNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:currencyFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:totalFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:paidFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:dueFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:invoiceDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppInvoiceBundle:DtModelStyle/SaleOrder:statusFormatter.html.twig', ['entity' => $entity, 'statuses' => $statuses,]);
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

        $qb = $this->getEntityManager()->getRepository('AppInvoiceBundle:SaleOrder')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['isDraft'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['relationName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['customerName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['customerCompanyName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['vendorName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['vendorCompanyName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['currency'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['total'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '9' && $criteria['bSortable_9'] === 'true') {
            $qb->orderBy($this->columns['paid'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '10' && $criteria['bSortable_10'] === 'true') {
            $qb->orderBy($this->columns['due'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '11' && $criteria['bSortable_11'] === 'true') {
            $qb->orderBy($this->columns['invoiceDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '12' && $criteria['bSortable_12'] === 'true') {
            $qb->orderBy($this->columns['status'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

    public function setStatusTranslator(StatusTranslator $statusTranslator)
    {
        $this->statusTranslator = $statusTranslator;
    }

}