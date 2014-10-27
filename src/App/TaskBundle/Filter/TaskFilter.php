<?php

namespace App\TaskBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class TaskFilter extends BaseFilter
{

    protected $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE;
    protected $columns      = [
        'id'          => 'App\TaskBundle\Entity\Task.id',
        'name'        => 'App\TaskBundle\Entity\Task.name',
        'project'     => 'project.name',
        'category'    => 'category.title',
        'type'        => 'App\TaskBundle\Entity\Task.name',
        'status'      => 'App\TaskBundle\Entity\Task.status',
        'tracker'     => 'tracker.name',
        'priority'    => 'priority.name',
        'assignedTo'  => 'assignedTo.firstName',
        'doneRatio'   => 'App\TaskBundle\Entity\Task.doneRatio',
        'InvoiceTask' => 'InvoiceTask.id',
        'startDate'   => 'App\TaskBundle\Entity\Task.startDate',
        'updatedAt'   => 'App\TaskBundle\Entity\Task.updatedAt',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:projectFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:categoryFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:typeInfoFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:statusInfoFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:trackerFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:priorityFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:personFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:progressFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:invoicesTasksFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:startDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppTaskBundle:DtModelStyle/Task:updatedAtFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppTaskBundle:Task')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['project'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['category'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['type'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['status'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['tracker'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['priority'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['assignedTo'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '9' && $criteria['bSortable_9'] === 'true') {
            $qb->orderBy($this->columns['doneRatio'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '10' && $criteria['bSortable_10'] === 'true') {
            $qb->orderBy($this->columns['InvoiceTask'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '11' && $criteria['bSortable_11'] === 'true') {
            $qb->orderBy($this->columns['startDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '12' && $criteria['bSortable_12'] === 'true') {
            $qb->orderBy($this->columns['updatedAt'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}