<?php

namespace App\TaskBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class TaskTrackerFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\TaskBundle\Entity\TaskTracker.id',
        'name'        => 'App\TaskBundle\Entity\TaskTracker.name',
        'description' => 'App\TaskBundle\Entity\TaskTracker.description',
        'color'       => 'App\TaskBundle\Entity\TaskTracker.color',
        'value'       => 'App\TaskBundle\Entity\TaskTracker.value',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskTracker:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskTracker:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskTracker:descriptionFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskTracker:colorFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskTracker:valueFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppTaskBundle:TaskTracker')->getQueryBuilderByCriteria($criteria);

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