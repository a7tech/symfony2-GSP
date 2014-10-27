<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.01.14
 * Time: 14:41
 */

namespace App\PurchaseBundle\Form\PurchaseWrapper;

use App\AccountProductBundle\Entity\AccountProduct;
use App\CompanyBundle\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;

class Item {

    /**
     * @var AccountProduct
     */
    protected $accountProduct;

    /**
     * @var Company
     */
    protected $supplier;

    /**
     * @var float
     *
     * @Assert\Type(type="numeric")
     * @Assert\NotBlank()
     */
    protected $price;

    /**
     * @var integer
     *
     * @Assert\Type(type="numeric")
     * @Assert\NotBlank()
     */
    protected $quantity;


    protected $taxes;

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param AccountProduct $product
     */
    public function setAccountProduct(AccountProduct $product)
    {
        $this->accountProduct = $product;
    }

    /**
     * @return AccountProduct
     */
    public function getAccountProduct()
    {
        return $this->accountProduct;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param \App\CompanyBundle\Entity\Company $supplier
     */
    public function setSupplier(Company $supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return \App\CompanyBundle\Entity\Company
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
    }

    public function getTaxes()
    {
        return $this->taxes;
    }
} 