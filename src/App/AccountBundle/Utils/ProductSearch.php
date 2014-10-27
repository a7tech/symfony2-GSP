<?php
/**
 * Created by PhpStorm.
 * User: nastya
 * Date: 1/29/14
 * Time: 10:08 PM
 */

namespace App\AccountBundle\Utils;

use App\CoreBundle\Filter\QueryBuilderFilter;
use Doctrine\ORM\QueryBuilder;

class ProductSearch extends QueryBuilderFilter
{
    protected function joinProperty(QueryBuilder $query_builder, $property)
    {
        switch($property){
            //join needed entities
            case 'brand':
                $query_builder->join('Product.brandGroup', 'BrandGroup');
                break;
            case 'supplier':
                $query_builder->join($this->repository->column('suppliers'), 'Supplier');
                break;
            case 'product_type':
                $query_builder->join('Product.productType', 'ProductType');
                break;
            case 'product_category':
                $query_builder->join('Product.categories', 'Category');
                break;
            case 'product_barcode':
                $query_builder->join('Product.barcodes', 'Barcode');
                break;
        }
    }

    protected function getCustomFilters()
    {
        return [
            'stock_level' => function(QueryBuilder $query_builder, $value) {
                $property = 'stock_level';
                $operation = null;
                $query_property = $this->parameters_mapping[$property];

                switch($value){
                    case 'zero_stock':
                        $operation = ' = 0 OR '.$query_property.' IS NULL';
                        break;
                    case 'negative':
                        $operation = ' < 0';
                        break;
                    case 'restocking':
                        $restocking_column = $this->repository->column('restockingQty');
                        $operation = ' < '.$restocking_column.' OR ('.$restocking_column.' > 0 AND '.$query_property.' IS NULL)';
                        break;
                }

                $query_builder->andWhere($query_property.$operation);
            }
        ];
    }


    protected function loadParametersMapping()
    {
        return [
            'product_type' => 'ProductType',
            'product_category' => 'Category',
            'brand' => 'BrandGroup',
            'supplier' => 'Supplier',
            'stock_availability' => $this->repository->column('stockAvailability'),
            'title' => 'Product.title',
            'id' => 'Product.id',
            'product_code' => 'Product.productCode',
            'product_barcode' => 'Barcode.number',
            'stock_level' => $this->repository->column('enQtyInc')
        ];
    }
} 