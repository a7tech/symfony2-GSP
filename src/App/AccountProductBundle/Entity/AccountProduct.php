<?php

namespace App\AccountProductBundle\Entity;

use App\AccountBundle\Entity\AccountProfile;
use App\ProductBundle\Entity\Product;
use App\TaxBundle\Entity\Taxation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccountProduct
 *
 * @ORM\Table(name="account_products")
 * @ORM\Entity(repositoryClass="App\AccountProductBundle\Entity\AccountProductRepository")
 */
class AccountProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var AccountProfile
     *
     * @ORM\ManyToOne(targetEntity="App\AccountBundle\Entity\AccountProfile")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     *
     */
    protected  $account;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="App\ProductBundle\Entity\Product", inversedBy="accProducts")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    protected  $product;

    /**
     * @var Price
     *
     * @ORM\ManyToOne(targetEntity="App\ProductBundle\Entity\Price", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="price_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $price;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_taxable", type="boolean", nullable=true)
     */
    protected $isTaxable;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\Taxation")
     * @ORM\JoinTable(name="account_products_taxes",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="taxation_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"number" = "ASC"})
     */
    protected $taxes;

    /**
     * @var boolean $canBeOrderedFromSupplier
     *
     * @ORM\Column(name="can_be_ordered_from_supplier", type="boolean", nullable=true)
     */
    protected $canBeOrderedFromSupplier;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CompanyBundle\Entity\Company", cascade={"persist"})
     * @ORM\JoinTable(name="products_suppliers",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $suppliers;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_be_stocked", type="boolean", nullable=true)
     */
    protected $canBeStocked;

    /**
     * @var integer
     *
     * @ORM\Column(name="sell_qty_unit", type="integer", nullable=true)
     */
    protected $sellQtyUnit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_be_delivered", type="boolean", nullable=true)
     */
    protected $canBeDelivered;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_qty_in_stock", type="integer", nullable=true)
     */
    protected $minQtyInStock;

    /**
     * @var integer
     *
     * @ORM\Column(name="en_qty_inc", type="integer", nullable=true)
     */
    protected $enQtyInc;

    /**
     * @var float
     *
     * @ORM\Column(name="restocking_qty", type="integer", nullable=true)
     */
    protected $restockingQty;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stock_availability", type="boolean", nullable=true)
     */
    protected $stockAvailability;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sell_out_of_stock", type="boolean", nullable=true)
     */
    protected $sellOutOfStock;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CompanyBundle\Entity\Company", cascade={"persist"})
     * @ORM\JoinTable(name="products_buyers",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $buyers;

    public function __construct() {

        $this->taxes = new ArrayCollection();
        $this->suppliers = new ArrayCollection();
        $this->buyers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \App\AccountProductBundle\Entity\AccountProfile $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return \App\AccountProductBundle\Entity\AccountProfile
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param \App\AccountProductBundle\Entity\Collection $buyers
     */
    public function addBuyer($buyer)
    {
        $this->buyers[] = $buyer;
    }

    public function removeBuyer($buyer)
    {
        $this->buyers->removeElement($buyer);
    }

    public function getBuyers()
    {
        return $this->buyers;
    }

    /**
     * @param Collection $buyers
     */
    public function setBuyers($buyers)
    {
        $this->buyers = $buyers;
    }

    /**
     * @param boolean $canBeDelivered
     */
    public function setCanBeDelivered($canBeDelivered)
    {
        $this->canBeDelivered = $canBeDelivered;
    }

    /**
     * @return boolean
     */
    public function getCanBeDelivered()
    {
        return $this->canBeDelivered;
    }

    /**
     * @param boolean $canBeOrderedFromSupplier
     */
    public function setCanBeOrderedFromSupplier($canBeOrderedFromSupplier)
    {
        $this->canBeOrderedFromSupplier = $canBeOrderedFromSupplier;
    }

    /**
     * @return boolean
     */
    public function getCanBeOrderedFromSupplier()
    {
        return $this->canBeOrderedFromSupplier;
    }

    /**
     * @param boolean $canBeStocked
     */
    public function setCanBeStocked($canBeStocked)
    {
        $this->canBeStocked = $canBeStocked;
    }

    /**
     * @return boolean
     */
    public function getCanBeStocked()
    {
        return $this->canBeStocked;
    }

    /**
     * @param boolean $isTaxable
     */
    public function setIsTaxable($isTaxable)
    {
        $this->isTaxable = $isTaxable;
    }

    /**
     * @return boolean
     */
    public function getIsTaxable()
    {
        return $this->isTaxable;
    }

    /**
     * @param int $minQtyInStock
     */
    public function setMinQtyInStock($minQtyInStock)
    {
        $this->minQtyInStock = $minQtyInStock;
    }

    /**
     * @return int
     */
    public function getMinQtyInStock()
    {
        return $this->minQtyInStock;
    }

    /**
     * @param \App\AccountProductBundle\Entity\Price $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return \App\AccountProductBundle\Entity\Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param float $restockingQty
     */
    public function setRestockingQty($restockingQty)
    {
        $this->restockingQty = $restockingQty;
    }

    /**
     * @return float
     */
    public function getRestockingQty()
    {
        return $this->restockingQty;
    }

    /**
     * @param boolean $sellOutOfStock
     */
    public function setSellOutOfStock($sellOutOfStock)
    {
        $this->sellOutOfStock = $sellOutOfStock;
    }

    /**
     * @return boolean
     */
    public function getSellOutOfStock()
    {
        return $this->sellOutOfStock;
    }

    /**
     * @param int $sellQtyUnit
     */
    public function setSellQtyUnit($sellQtyUnit)
    {
        $this->sellQtyUnit = $sellQtyUnit;
    }

    /**
     * @return int
     */
    public function getSellQtyUnit()
    {
        return $this->sellQtyUnit;
    }

    /**
     * @param boolean $stockAvailability
     */
    public function setStockAvailability($stockAvailability)
    {
        $this->stockAvailability = $stockAvailability;
    }

    /**
     * @return boolean
     */
    public function getStockAvailability()
    {
        return $this->stockAvailability;
    }

    public function addSupplier($supplier)
    {
        $this->suppliers[] = $supplier;
    }

    public function removeSupplier($supplier)
    {
        $this->suppliers->removeElement($supplier);
    }

    public function getSuppliers()
    {
        return $this->suppliers;
    }

    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
    }

    /**
     * @param Taxation $tax
     */
    public function addTax($tax)
    {
        $this->taxes[] = $tax;
    }

    public function removeTax($tax)
    {
        $this->taxes->removeElement($tax);
    }

    /**
     * @return \App\AccountProductBundle\Entity\Collection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param int $enQtyInc
     */
    public function setEnQtyInc($enQtyInc)
    {
        $this->enQtyInc = $enQtyInc;
    }

    /**
     * @return int
     */
    public function getEnQtyInc()
    {
        return $this->enQtyInc;
    }

    function __toString()
    {
        return (string)$this->product;
    }


}
