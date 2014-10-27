<?php

namespace App\SocialMediaBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class SocialMediaTypeFilter extends BaseFilter
{

    protected $columns = [
        'no'   => 'App\SocialMediaBundle\Entity\SocialMediaType.id',
        'id'   => 'App\SocialMediaBundle\Entity\SocialMediaType.id',
        'name' => 'App\SocialMediaBundle\Entity\SocialMediaType.name',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppSocialMediaBundle:DtModelStyle/SocialMediaType:noFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppSocialMediaBundle:DtModelStyle/SocialMediaType:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppSocialMediaBundle:DtModelStyle/SocialMediaType:nameFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppSocialMediaBundle:SocialMediaType')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['no'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}