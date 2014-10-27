<?php

namespace App\ProjectBundle\Filter;

use App\ProjectBundle\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class ProjectFilter extends BaseFilter
{

    protected $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE;
    protected $columns      = [
        'id'             => 'App\ProjectBundle\Entity\Project.id',
        'name'           => 'App\ProjectBundle\Entity\Project.name',
        'type'           => 'App\ProjectBundle\Entity\Project.type',
        'status'         => 'App\ProjectBundle\Entity\Project.status',
        'accountProfile' => 'accountProfile.name',
        'opportunity'    => 'opportunity.name',
        'owner'          => 'ownerPerson.firstName',
        'client'         => 'clientPerson.firstName',
        'manager'        => 'managerPerson.firstName',
        'progress'       => 'App\ProjectBundle\Entity\Project.progress',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            /** @var Project $entity */
            $arguments = [
                'entity' => $entity
            ];

            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:idFormatter.html.twig', $arguments);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:nameFormatter.html.twig', $arguments);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:typeFormatter.html.twig', $arguments);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:statusFormatter.html.twig', $arguments);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:accountProfileFormatter.html.twig', $arguments);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:opportunityFormatter.html.twig', $arguments);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:personFormatter.html.twig', ['user' => $entity->getOwner()]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:personFormatter.html.twig', ['user' => $entity->getClient()]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/Project:personFormatter.html.twig', ['user' => $entity->getManager()]);
            $results[$count][]           = $renderer->render('AppBackendBundle:DataTable:date.html.twig', ['date' => $entity->getRealStartDate()]);
            $results[$count][]           = $renderer->render('AppBackendBundle:DataTable:date.html.twig', ['date' => $entity->getRealEndDate()]);
            $results[$count][]           = $renderer->render('AppBackendBundle:DataTable:progress.html.twig', ['progress' => $entity->getProgress(true)]);
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

        $qb = $this->getEntityManager()->getRepository('AppProjectBundle:Project')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['type'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['status'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['accountProfile'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['owner'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['client'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '11' && $criteria['bSortable_11'] === 'true') {
            $qb->orderBy($this->columns['progress'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}