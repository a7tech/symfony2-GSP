<?php

namespace App\PersonBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Filter\Filter as BaseFilter;

class PersonFilter extends BaseFilter
{

    protected $columns = [
        'id'          => 'App\PersonBundle\Entity\Person.id',
        'name'        => 'App\PersonBundle\Entity\Person.firstName',
        'lastName'    => 'App\PersonBundle\Entity\Person.lastName',
        'personGroup' => 'App\PersonBundle\Entity\Person.id',
        'phoneType'   => 'App\PersonBundle\Entity\Person.id',
        'addresses'   => 'App\PersonBundle\Entity\Person.id',
        'employments' => 'App\PersonBundle\Entity\Person.id',
        'emails'      => 'App\PersonBundle\Entity\Person.id',
        'gender'      => 'App\PersonBundle\Entity\Person.gender',
        'createdAt'   => 'App\PersonBundle\Entity\Person.createdAt',
        'updatedAt'   => 'App\PersonBundle\Entity\Person.updatedAt',
    ];

    public function getDataFormatter($data, $countRows)
    {
        $renderer = $this->getTemplating();
        $count    = 0;
        $results  = [];

        foreach ($data as $entity) {
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:idFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:nameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:lastNameFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:personGroupFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:phonesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:addressesFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:employmentsFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:emailsFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:genderFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:createdAtFormatter.html.twig', ['entity' => $entity,]);
            $results[$count][]           = $renderer->render('AppPersonBundle:DtModelStyle/Person:updatedAtFormatter.html.twig', ['entity' => $entity,]);
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

        $qb = $this->getEntityManager()->getRepository('AppPersonBundle:Person')->getQueryBuilderByCriteria($criteria);

        if ($criteria['iSortCol_0'] === '0' && $criteria['bSortable_0'] === 'true') {
            $qb->orderBy($this->columns['id'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '1' && $criteria['bSortable_1'] === 'true') {
            $qb->orderBy($this->columns['name'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '2' && $criteria['bSortable_2'] === 'true') {
            $qb->orderBy($this->columns['lastName'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '3' && $criteria['bSortable_3'] === 'true') {
            $qb->orderBy($this->columns['personGroup'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '4' && $criteria['bSortable_4'] === 'true') {
            $qb->orderBy($this->columns['phoneType'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '5' && $criteria['bSortable_5'] === 'true') {
            $qb->orderBy($this->columns['addresses'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '6' && $criteria['bSortable_6'] === 'true') {
            $qb->orderBy($this->columns['employments'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '7' && $criteria['bSortable_7'] === 'true') {
            $qb->orderBy($this->columns['emails'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '8' && $criteria['bSortable_8'] === 'true') {
            $qb->orderBy($this->columns['gender'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '9' && $criteria['bSortable_9'] === 'true') {
            $qb->orderBy($this->columns['createdAt'], $request->query->get('sSortDir_0'));
        } else if ($criteria['iSortCol_0'] === '10' && $criteria['bSortable_10'] === 'true') {
            $qb->orderBy($this->columns['updatedAt'], $request->query->get('sSortDir_0'));
        }

        return $qb;
    }

}