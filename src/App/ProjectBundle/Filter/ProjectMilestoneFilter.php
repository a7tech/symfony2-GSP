<?php

namespace App\ProjectBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class ProjectMilestoneFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\ProjectBundle\Entity\ProjectMilestone.id',
        'name'        => 'App\ProjectBundle\Entity\ProjectMilestone.name',
        'description' => 'App\ProjectBundle\Entity\ProjectMilestone.description',
        'status'      => 'App\ProjectBundle\Entity\ProjectMilestone.status',
        'value'       => 'App\ProjectBundle\Entity\ProjectMilestone.value',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][] = $renderer->render('AppProjectBundle:DtModelStyle/ProjectMilestone:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][] = $renderer->render('AppProjectBundle:DtModelStyle/ProjectMilestone:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][] = $renderer->render('AppProjectBundle:DtModelStyle/ProjectMilestone:descriptionFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][] = $renderer->render('AppProjectBundle:DtModelStyle/ProjectMilestone:statusFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][] = $renderer->render('AppProjectBundle:DtModelStyle/ProjectMilestone:valueFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppProjectBundle:ProjectMilestone')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['description'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['status'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['value'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}