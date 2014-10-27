<?php

/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.01.14
 * Time: 11:13
 */

namespace App\CoreBundle\Entity;

use App\CoreBundle\Utils\Formatter;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;

class EntityRepository extends BaseEntityRepository
{

    public function getAlias()
    {
        return $this->getEntityName();
    }

    /**
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getDefaultQueryBuilder()
    {
        $query_builder = $this->createQueryBuilder($this->getEntityName());
        return $query_builder;
    }

    /**
     * Gets all entities.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->getDefaultQueryBuilder()->getQuery()->getResult();
    }

    /**
     * A helper to build a column name with joined entity name.
     *
     * @param string $name Raw name of the column.
     * @return string Name of the column with preceding entity name.
     */
    public function column($name)
    {
        return $this->getEntityName() . '.' . $name;
    }

    /**
     * Gets entity by id
     *
     * @param integer $id
     * @return object
     */
    public function getById($id)
    {
        return $this->getDefaultQueryBuilder()
                        ->andWhere($this->column('id') . ' = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

    /**
     * Gets entities by IDS
     *
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids)
    {
        return $this->getDefaultQueryBuilder()
                        ->andWhere($this->column('id') . ' IN (:ids)')
                        ->setParameter('ids', $ids)
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Adds order by to the query builder.
     *
     * @param QueryBuilder $query_builder
     * @param array $ordering Ordering. Key is the column, value is ASC/DESC.
     * @param string $alias[optional] Optional alias to use with the column.
     * @param bool $reset[optional] Reset previous ordering? Default: false.
     * @return QueryBuilder
     */
    public function addOrdering(QueryBuilder $query_builder, array $ordering = array(), $alias = null, $reset = false)
    {
        if ($reset) {
            $query_builder->resetDQLPart('orderBy');
        }

        foreach ($ordering as $column => $order) {
            $column = (!empty($alias)) ? $alias . '.' . $column : $this->column($column);
            $order  = ($order === 'DESC') ? 'DESC' : 'ASC';

            $query_builder->addOrderBy($column, $order);
        }

        return $query_builder;
    }

    public function __call($method, $arguments)
    {
        if (strpos($method, 'getBy') === 0) {
            $argument = Formatter::toCamelCase(substr($method, 5));

            return $this->getBy(array($argument => $arguments[0]));
        } elseif (strpos($method, 'getAllBy') === 0) {
            $argument = Formatter::toCamelCase(substr($method, 8));

            return $this->getAllBy(array($argument => $arguments[0]));
        } else {
            return parent::__call($method, $arguments);
        }
    }

    public function getBy(array $criteria, $single_result = true)
    {
        $query_builder = $this->getQueryBuilderWithCriteria($criteria);

        $query = $query_builder->getQuery();

        return $single_result ? $query->getOneOrNullResult() : $query->getResult();
    }

    /**
     * Gets query builder with applied criteria passed as parameter
     *
     * @param array $criteria
     * @return QueryBuilder
     */
    public function getQueryBuilderWithCriteria($criteria)
    {
        $query_builder = $this->getDefaultQueryBuilder();

        foreach ($criteria as $parameter => $value) {
            $is_array = is_array($value);

            $where = $this->column($parameter) . ' '
                    . ($is_array ? 'IN (' : '= ')
                    . ':' . $parameter;

            if ($is_array) {
                //close
                $where .= ')';
            }

            $query_builder->andWhere($where)
                    ->setParameter($parameter, $value);
        }

        return $query_builder;
    }

    public function getAllBy(array $criteria)
    {
        return $this->getBy($criteria, false);
    }

    public function getQueryBuilderByCriteria(array $criteria = array())
    {
        $qb = $this->getDefaultQueryBuilder();

        if (!isset($criteria['isAlreadySorted']) || $criteria['isAlreadySorted'] === false) {
            $qb->orderBy($this->getAlias() . '.id', 'DESC');
        }

        if (isset($criteria['limit']) && $criteria['limit'] !== '' && $criteria['limit'] !== null) {
            $qb->setMaxResults($criteria['limit']);
        }

        if (isset($criteria['enabled']) && $criteria['enabled'] !== '' && $criteria['enabled'] !== null) {
            $qb
                    ->andWhere($this->getAlias() . '.enabled = :enabled')
                    ->setParameter('enabled', $criteria['enabled'])
            ;
        }

        return $qb;
    }

}