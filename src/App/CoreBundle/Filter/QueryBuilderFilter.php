<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 04.02.14
 * Time: 13:57
 */

namespace App\CoreBundle\Filter;


use App\CoreBundle\Entity\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;

abstract class QueryBuilderFilter
{

    /**
     * @var \App\CoreBundle\Entity\EntityRepository
     */
    protected $repository;

    protected $parameters_mapping;

    protected $operations_mapping = [
        'in' => 'IN (__parameter__)',
        'not_in' => 'NOT IN (__parameter__)',
        'starts_with' => 'LIKE LOWER(__parameter__)',
        'doesnt_start_with' => 'NOT LIKE LOWER(__parameter__)',
        'equals' => '= LOWER(__parameter__)',
        'contains' => 'LIKE LOWER(__parameter__)',
        'less_equal_than' => ' <= __parameter__',
        'more_equal_than' => ' >= __parameter__',
        'equal' => '= __parameter__',
        'not_equal' => '<> __parameter__'
    ];

    protected $text_search_operations = [
        'starts_with',
        'doesnt_start_with',
        'equals',
        'contains'
    ];

    protected $custom_filters;

    /**
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getParametersMapping(){
        if($this->parameters_mapping === null){
            $this->parameters_mapping = $this->loadParametersMapping();
        }

        return $this->parameters_mapping;
    }

    abstract protected function loadParametersMapping();

    protected function joinProperty(QueryBuilder $query_builder, $property) { }

    protected function getCustomFilters(){
        return [];
    }

    public function filter(QueryBuilder $query_builder, $search_parameters) {

        $parameters_mapping = $this->getParametersMapping();
        $custom_filters = $this->getCustomFilters();

        foreach($search_parameters as $property => $value){
            $is_array = is_array($value);
            if(isset($parameters_mapping[$property]) && (($is_array && isset($value['value']) && $value['value'] != '') || (!$is_array && $value != ''))){
                //property mapping exist and search value is not empty - continue!
                $this->joinProperty($query_builder, $property);

                if(is_array($value)){
                    if(!($value['value'] instanceof ArrayCollection) || $value['value']->count() > 0){

                        //filter with comparator modifier
                        $operation = $this->operations_mapping[$value['operation']];
                        $operation = str_replace('__parameter__', ':'.$property, $operation);

                        //value normalization for string search
                        $query_value = $value['value'];
                        if(in_array($value['operation'], $this->text_search_operations)){
                            $query_value = mb_strtolower($query_value);
                        }

                        switch($value['operation']){
                            case 'starts_with':
                            case 'doesnt_start_with':
                                $query_value = $query_value.'%';
                                break;
                            case 'contains':
                                $query_value = '%'.$query_value.'%';
                                break;
                        }

                        if($query_value instanceof Collection){
                            $query_value = $query_value->toArray();
//                            var_dump(count($query_value));
                        }

                        $query_builder->andWhere($parameters_mapping[$property].' '.$operation)
                            ->setParameter($property, $query_value);
                    }
                } else {
                    //simple filters
                    if(isset($custom_filters[$property])){
                        call_user_func($custom_filters[$property], $query_builder, $value);
                    } else {
                        $query_builder->andWhere($parameters_mapping[$property].' = :'.$property)
                            ->setParameter($property, $value);
                    }
                }
            }
        }

        return $query_builder;
    }
}