<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-17
 * Time: 11:18
 */

namespace App\InvoiceBundle\Form\Transformer;


use App\InvoiceBundle\Entity\InvoiceProduct;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProductsReturnsTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $invoiceProducts;

    function __construct($invoiceProducts)
    {
        $this->invoiceProducts = $invoiceProducts;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        //transform product to product order number
        if($value instanceof InvoiceProduct){
            return $value->getProduct()->getId();
        }

        return null;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        //transform order back to product
        if(!empty($value)){
            /** @var InvoiceProduct|null $product */
            foreach($this->invoiceProducts as $product){
                if($product->getProduct()->getId() == $value){
                    return $product;
                }
            }
        }

        throw new TransformationFailedException('Given "order" doesn\'t match any product order');

    }


} 