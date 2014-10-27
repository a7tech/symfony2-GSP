<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 04.02.14
 * Time: 15:32
 */

namespace App\TaskBundle\Filter;


use App\CoreBundle\Filter\QueryBuilderFilter;
use Doctrine\ORM\QueryBuilder;

class TasksQueryBuilderFilter extends QueryBuilderFilter
{
    protected function loadParametersMapping()
    {
        return [
            'project' => $this->repository->column('project'),
            'tracker' => $this->repository->column('tracker'),
            'name' => $this->repository->column('name'),
            'status' => $this->repository->column('status'),
            'priority' => $this->repository->column('priority'),
            'assigned_to' => 'AssignedPerson',
            'doneRatio' => $this->repository->column('doneRatio'),
            'start_date' => $this->repository->column('startDay'),
            'end_date' => $this->repository->column('closedAt'),
            'category' => $this->repository->column('category')
        ];
    }

    protected function joinProperty(QueryBuilder $query_builder, $property)
    {
        switch($property){
            case 'assigned_to':
                $query_builder->leftJoin($this->repository->column('assignedTo'), 'AssignedPerson');
        }
    }


} 