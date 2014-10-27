<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 22.01.14
 * Time: 02:13
 */

namespace App\PurchaseBundle\Form\Type;


use App\CompanyBundle\Entity\CompanyRepository;
use App\ProductBundle\Entity\ProductTypeRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('product_type', 'entity_search', [
            'required' => false,
            'entity_options' => [
                'class' => 'App\\ProductBundle\\Entity\\ProductType',
                'query_builder' => function(ProductTypeRepository $repository){
                    return $repository->getDefaultQueryBuilder();
                }
            ]
        ])->add('product_category', 'entity_search', [
            'required' => false,
            'entity_options' => [
                'class' => 'App\\ProductBundle\\Entity\\Category',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('a')
                        ->orderBy('a.title', 'ASC');
                }
            ]
        ])->add('brand', 'entity_search', [
            'required' => false,
            'entity_options' => [
                'class' => 'App\\ProductBundle\\Entity\\BrandGroup',
                'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('a')
                            ->orderBy('a.title', 'ASC');
                    }
            ]
        ])->add('supplier', 'entity_search', [
            'required' => false,
            'entity_options' => [
                'class' => 'App\\CompanyBundle\\Entity\\Company',
                'query_builder' => function(CompanyRepository $repository){
                        return $repository->getDefaultQueryBuilder();
                    }
            ]
        ])->add('stock_availability', 'choice', [
            'required' => false,
            'choices' => [
                1 => 'In stock',
                0 => 'Out of stock/discontinued'
            ],
            'attr' => [
                //'class' => 'form-control single'
                'class' => 'form-control'
            ]
        ])->add('title', 'text_search', [
            'label' => 'Name',
            'required' => false
        ])->add('id', 'text_search', [
            'label' => 'ID',
            'required' => false
        ])->add('product_code', 'text_search', [
            'label' => 'Product code / MPN / SKU',
            'required' => false
        ])->add('product_barcode', 'text_search', [
            'label' => 'Barcode UPC / EAN',
            'required' => false
        ])->add('stock_level', 'choice', [
                'label' => 'Stock level',
                'required' => false,
                'choices' => [
                    '' => 'All products',
                    'zero_stock' => 'Without stock level',
                    'negative' => 'At negative (under zero) stock level',
                    'restocking' => 'At restocking alert'
                ],
                'attr' => [
                    //'class' => 'form-control single'
                    'class' => 'form-control'
                ]
            ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_purchase_search';
    }

} 