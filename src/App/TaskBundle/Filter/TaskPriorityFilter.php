<?php

namespace App\TaskBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class TaskPriorityFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\TaskBundle\Entity\TaskPriority.id',
        'name'        => 'App\TaskBundle\Entity\TaskPriority.name',
        'description' => 'App\TaskBundle\Entity\TaskPriority.description',
        'color'       => 'App\TaskBundle\Entity\TaskPriority.color',
        'value'       => 'App\TaskBundle\Entity\TaskPriority.value',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskPriority:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskPriority:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskPriority:descriptionFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskPriority:colorFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/TaskPriority:valueFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppTaskBundle:TaskPriority')->getQueryBuilderByCriteria($criteria);

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