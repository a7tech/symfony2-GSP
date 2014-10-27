<?php

namespace App\CoreBundle\Filter;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class Filter implements ContainerAwareInterface
{

    CONST DEFAULT_ITEMS_PER_PAGE  = 10;

    protected $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE;

    /**
     *
     * @var EngineInterface 
     */
    protected $templating;

    /**
     *
     * @var ContainerInterface 
     */
    protected $container;

    /**
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;
    protected $dataFormatter;
    protected $columns;

    public function processRequest($request)
    {
        if (!$this->isAjaxRequest($request)) {
            return false;
        }

        return $this->getJsonResponse($request);
    }

    /**
     * getData
     *
     * override this function to return a raw data array
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getData(Request $request)
    {
        return $this->getDataByQueryBuilder($request, $this->getQueryBuilder($request));
    }

    protected function getDataByQueryBuilder(Request $request, QueryBuilder $qb)
    {
        $searchKeyword = $request->query->get('sSearch');

        if (isset($request)) {
            $getParameters = $request->query->all();
        }
        
        $columns = $this->getColumns();

        if ($searchKeyword !== '') {
            $parameters = [];
            $orX        = new \Doctrine\ORM\Query\Expr\Orx();
            foreach ($columns as $key => $column) {
                $orX->add($qb->expr()->like("{$column}", ":{$key}"));
                $parameters[$key] = "%" . $searchKeyword . "%";
            }

            $orX = (string) $orX;

            if (!empty($orX)) {
                $qb->andWhere($orX);

                foreach ($parameters as $key => $value) {
                    $qb->setParameter("$key", $value);
                }
            }
        }
        
        
        $countQuery = clone $qb->getQuery();
        $params = $qb->getQuery()->getParameters();
        
        foreach ($params as $key => $param) {
            $countQuery->setParameter($key, $param);
        }
        
        $countQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('App\CoreBundle\ORM\Paginate\CountSqlWalker'));
        
        $countQuery->setFirstResult(null)->setMaxResults(null);
        $countQuery->setParameters($qb->getQuery()->getParameters());
        
        $countRows = $countQuery->getSingleScalarResult();
        
        if (isset($getParameters['iDisplayLength'])) {
            $qb->setMaxResults($getParameters['iDisplayLength']);
        }
        
        if (isset($getParameters['iDisplayStart'])) {
            $qb->setFirstResult($getParameters['iDisplayStart']);
        }
        
        return $this->getDataFormatter($qb->getQuery()->getResult(), $countRows);
    }

    /**
     * isAjaxRequest
     *
     * @param Request $request
     * @return bool
     */
    public function isAjaxRequest(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getDataFormatter($data, $count)
    {
        return $this->dataFormatter;
    }

    protected function getQueryBuilder(Request $request)
    {
        return $this->queryBuilder;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em = null)
    {
        $this->em = $em;
    }

    /**
     * 
     * @return type
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function getRepository($entityName)
    {
        return $this->getEntityManager()->getRepository($entityName);
    }

    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;

        return $this;
    }

    protected function getTemplating()
    {
        return $this->templating;
    }

    protected function renderView($view, array $parameters = array())
    {
        return $this->getTemplating()->render($view, $parameters);
    }

    protected function denyNoXmlRequests(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException('Access denied.');
        }
    }

    /**
     * getJsonResponse
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getJsonResponse(Request $request)
    {
        return new JsonResponse($this->getData($request));
    }

    public function getColumns()
    {
        return $this->columns;
    }

}