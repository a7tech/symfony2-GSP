<?php

namespace App\LanguageBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class LanguageFilter extends BaseFilter
{

    protected $columns = [
        'id'              => 'App\LanguageBundle\Entity\Language.iso',
        'name'            => 'App\LanguageBundle\Entity\Language.name',
        'flag'            => 'App\LanguageBundle\Entity\Language.iso',
        'enabledFrontend' => 'App\LanguageBundle\Entity\Language.iso',
        'enabledBackend'  => 'App\LanguageBundle\Entity\Language.iso',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppLanguageBundle:DtModelStyle/Language:idFormatter.html.twig', ['lang' => $entity,]);
            $results[$count][]           = $renderer->render('AppLanguageBundle:DtModelStyle/Language:titleFormatter.html.twig', ['lang' => $entity,]);
            $results[$count][]           = $renderer->render('AppLanguageBundle:DtModelStyle/Language:flagFormatter.html.twig', ['lang' => $entity,]);
            $results[$count][]           = $renderer->render('AppLanguageBundle:DtModelStyle/Language:enabledFrontendFormatter.html.twig', ['lang' => $entity,]);
            $results[$count][]           = $renderer->render('AppLanguageBundle:DtModelStyle/Language:enabledBackendFormatter.html.twig', ['lang' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppLanguageBundle:Language')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['flag'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['enabledFrontend'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['enabledBackend'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}