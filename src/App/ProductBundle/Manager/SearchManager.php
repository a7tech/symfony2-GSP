<?php

/**
 * Created by Ricardo Renteria.
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\ProductBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class SearchManager
{
    protected $container;
    
    /**
     * Constructor
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * Return elasticsearch data
     *
     * @return object
     */
    public function search($parameters)
    {
        $esType = $this->container->get('fos_elastica.index.gsp.product');

        //Create query
        $query = $this->createElasticaQuery($parameters);
        $resultSet = $esType->search($query);
        $result = array();

        foreach ($resultSet as $key => $data ) {
            $result[$key] = $data->getData();
        }

        return $result;
    }
       
    /**
     * Return elasticsearch query
     * 
     * @return \Elastica\Query\Bool 
     */
    protected function createElasticaQuery($parameters)
    {

        $searchParams = array();
        $limit = $parameters['limit'];
        $startIndex = ($parameters['page'] - 1) * $parameters['limit'];
        
        if ( !empty($parameters) && isset($parameters['search'])){
            $params = $this->sanitize($parameters['search']);
        }

         // query match all
        $esMatch  = new \Elastica\Query\MatchAll();

        // Filter 'and' for the parameters search
        $esFilterBoolAnd    = new \Elastica\Filter\BoolAnd();

        $query = new \Elastica\Query();

        $addFilter = false;

        if (!empty($params)){
            foreach ($params as $k => $v )
            {
                switch($k){
                    case 'title':
                    case 'productCode':
                    case 'isActive':
                        $term = new \Elastica\Filter\Term();
                        $term->setTerm($k, array(strtolower($v['value'])));
                        $esFilterBoolAnd->addFilter($term);
                        unset($term);
                        $addFilter = true;
                        break;
                    case 'categories':
                    case 'brandGroup':
                        $term  = new \Elastica\Query\Term();
                        $nested  = new \Elastica\Filter\Nested();
                        $nested->setPath($k);
                        $term->setTerm($k.'.id', $v['value']); 
                        $nested->setQuery($term);
                        $esFilterBoolAnd->addFilter($nested);
                        unset($term, $nested);
                        $addFilter = true;
                        break;

                }
            }
            /*
            if ( !empty($params['createdAtFrom']) && !empty($params['createdAtTo']) ){
                $esRange  = new \Elastica\Filter\Range();
                $esRange->addField('createdAt', array('from' => $params['createdAtFrom']['value'], 'to' => $params['createdAtTo']['value']));  
                $esFilterBoolAnd->addFilter($esRange);
                unset($esRange);
                $addFilter = true;
            }
            */

            if($addFilter == true){
                $query->setFilter($esFilterBoolAnd);
            }
        }

        //$query->setSort(array($config['sort']['field'] => $config['sort']['option']));
        $query->setFrom($startIndex);
        $query->setLimit($limit);
        
        return $query;
    }

    private function sanitize($parameters, $indexName='product')
    {
        $params = array();
        $config = $this->container->parameters['search'];
        $configArr = $config[$indexName];

        foreach($parameters as $key => $value){
            if (array_key_exists($key, $configArr) && trim($value) !='') {
                $valArr = $configArr[$key];
                $valArr['value'] = $value;
                $params[$key] = $valArr;
            }
        }

        return $params;
    }
    
   
}
