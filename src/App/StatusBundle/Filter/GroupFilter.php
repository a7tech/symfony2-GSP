<?php

namespace App\StatusBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class GroupFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'statuses.id',
        'name'        => 'statuses.name',
        'description' => 'statuses.description',
        'color'       => 'statuses.color',
        'value'       => 'statuses.value',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];
        $entities = $data[0]->getStatuses();

        foreach ($entities as $entity) {
            $results[$count][]           = $renderer->render('AppStatusBundle:DtModelStyle/Group:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppStatusBundle:DtModelStyle/Group:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppStatusBundle:DtModelStyle/Group:descriptionFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppStatusBundle:DtModelStyle/Group:colorFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppStatusBundle:DtModelStyle/Group:valueFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppStatusBundle:Group')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['description'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['color'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['value'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}