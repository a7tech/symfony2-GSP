<?php

namespace App\ProjectBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class ProjectOpportunityFilter extends BaseFilter
{

    protected $columns = [
        'id'              => 'App\ProjectBundle\Entity\ProjectOpportunity.id',
        'name'            => 'App\ProjectBundle\Entity\ProjectOpportunity.name',
        'accountProfile'  => 'accountProfile.name',
        'personFirstName' => 'person.firstName',
        'milestone'       => 'milestone.name',
        'expectedValue'   => 'App\ProjectBundle\Entity\ProjectOpportunity.expectedValue',
        'currency'        => 'currency.name',
        'client'          => 'client.firstName',
        'commision'       => 'App\ProjectBundle\Entity\ProjectOpportunity.commision',
        'expectedDate'    => 'App\ProjectBundle\Entity\ProjectOpportunity.expectedDate',
        'project'         => 'project.name',
        'progress'        => 'App\ProjectBundle\Entity\ProjectOpportunity.progress',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:accountProfileFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:ownerFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:milestoneFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:expectedValueFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:currencyFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:clientFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:commisionFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:expectedDateFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:projectFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppProjectBundle:DtModelStyle/ProjectOpportunity:progressFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppProjectBundle:ProjectOpportunity')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['accountProfile'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['personFirstName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['milestone'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['expectedValue'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['currency'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['client'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['commision'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '9' && $criteria['bSortable_9'] === 'true') {
            $qb->orderBy($this->columns['expectedDate'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '10' && $criteria['bSortable_10'] === 'true') {
            $qb->orderBy($this->columns['project'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_1'] === '11' && $criteria['bSortable_11'] === 'true') {
            $qb->orderBy($this->columns['progress'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}