<?php

/**
 * Created by Ricardo Renteria.
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\CompanyBundle\Manager;

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
    	$esType = $this->container->get('fos_elastica.index.gsp.company');

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
                    case 'name':
                        $term = new \Elastica\Filter\Term();
                        $term->setTerm($k, array(strtolower($v['value'])));
                        $esFilterBoolAnd->addFilter($term);
                        unset($term);
                        $addFilter = true;
                        break;
                    case 'sector':
                    case 'companyType':
                    case 'specialities':
                    case 'socialMedias':
                        $term  = new \Elastica\Query\Term();
                        $nested  = new \Elastica\Filter\Nested();
                        switch($k){
                            case 'socialMedias':
                                $nested->setPath("socialMedias.socialMediaType");
                                $term->setTerm('socialMedias.socialMediaType.id', $v['value']);
                                break;
                            case 'companyType':
                            case 'specialities':
                            case 'sector':
                                $nested->setPath($k);
                                $term->setTerm($k.'.id', $v['value']);
                                break;
                        }
                        $nested->setQuery($term);
                        $esFilterBoolAnd->addFilter($nested);
                        unset($term, $nested);
                        $addFilter = true;
                        break;
                    //case 'phoneType':
                    //case 'addressType':
                    //case 'country':
                    case 'state':
                    case 'street':
                    case 'city':
                    case 'postcode':
                        $term  = new \Elastica\Query\Term();
                        $nested  = new \Elastica\Filter\Nested();
                        switch($k){
                            case 'state':
                            case 'street':
                            case 'city':
                            case 'postcode':
                                $nested->setPath("addresses");
                                $term->setTerm('addresses.'.$k, array(strtolower($v['value']))); 
                                break;
                        }
                        $nested->setQuery($term);
                        $esFilterBoolAnd->addFilter($nested);
                        unset($term, $nested);
                        $addFilter = true;
                        break;
                }
            }

            if($addFilter == true){
                $query->setFilter($esFilterBoolAnd);
            }
        }
        
    	$query->setFrom($startIndex);
    	$query->setLimit($limit);
        
        return $query;
    }
    
    private function sanitize($parameters, $indexName='company')
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