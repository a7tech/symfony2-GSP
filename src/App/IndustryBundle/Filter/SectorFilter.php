<?php

namespace App\IndustryBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class SectorFilter extends BaseFilter
{

    protected $columns = [
        'id'           => 'App\IndustryBundle\Entity\Sector.id',
        'title'        => 'App\IndustryBundle\Entity\Sector.title',
        'specialities' => 'App\IndustryBundle\Entity\Sector.id',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppIndustryBundle:DtModelStyle/Sector:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppIndustryBundle:DtModelStyle/Sector:titleFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppIndustryBundle:DtModelStyle/Sector:specialitiesFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppIndustryBundle:Sector')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['title'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['specialities'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}