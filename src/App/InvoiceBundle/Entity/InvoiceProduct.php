<?php

namespace App\InvoiceBundle\Entity;

use App\AccountProductBundle\Entity\AccountProduct;
use App\TaxBundle\Entity\Taxation;
use App\TaxBundle\Entity\TaxationCopy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * InvoiceProduct
 *
 * @ORM\Table(name="invoice_products")
 * @ORM\Entity(repositoryClass="App\InvoiceBundle\Entity\InvoiceProductRepository")
 * @Assert\Callback(methods={"areReturnsValid"})
 */
class InvoiceProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\GreaterThan(value=0, message="Value must be greater than 0")
     * @Assert\Type(type="integer", message="Only numeric values allowed")
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @var integer
     * @Assert\GreaterThan(value=0, message="Value must be greater than 0")
     * @Assert\Type(type="float", message="Only numeric values allowed")
     * @ORM\Column(name="price", type="float")
     */
    protected $price;

    /**
     * @var AccountProduct
     * @ORM\ManyToOne(targetEntity="App\AccountProductBundle\Entity\AccountProduct")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @var string
     * @ORM\Column(name="product_name", type="string", nullable=true)
     */
    protected $productName;

    /**
     * @var string
     * @ORM\Column(name="product_code", type="string", nullable=true)
     */
    protected $productCode;

    /**
     * @var SaleOrder
     * @ORM\ManyToOne(targetEntity="SaleOrder", inversedBy="products")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     **/
    protected $order;

    /**
     * @var float
     * @Assert\Range(
     *      min = 0,
     *      max = 1,
     *      minMessage = "Discount cannot be lower than 0%",
     *      maxMessage = "Discount cannot be higher than 100%"
     * )
     * @Assert\Type(type="float", message="Only numeric values allowed")
     * @ORM\Column(name="discount", type="float")
     */
    protected $discount = 0;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\Taxation", cascade={"persist"})
     * @ORM\JoinTable(name="invoice_products_taxes",
     *      joinColumns={@ORM\JoinColumn(name="invoice_product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="taxation_id", referencedColumnName="id")}
     * )
     */
    protected $taxes;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\TaxationCopy", cascade={"persist"})
     * @ORM\JoinTable(name="invoice_products_taxes_copy",
     *      joinColumns={@ORM\JoinColumn(name="invoice_product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="taxation_copy_id", referencedColumnName="id")}
     * )
     */
    protected $taxesCopy;

    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\InvoiceProductReturn", mappedBy="invoiceProduct", cascade={"all"})
     * @Assert\Valid
     */
    protected $productReturns;

    public function __construct() {
        $this->taxes = new ArrayCollection();
        $this->productReturns = new ArrayCollection();
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
     * Set quantity
     *
     * @param string $quantity
     * @return InvoiceProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    
        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return InvoiceProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param AccountProduct $product
     */
    public function setProduct(AccountProduct $product)
    {

        $this->product = $product;
    }

    /**
     * @return AccountProduct
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = floatval($discount);
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param \App\InvoiceBundle\Entity\Collection $taxes
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
    }

    /**
     * @return \App\InvoiceBundle\Entity\Collection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @return Collection
     */
    public function getTaxesCopy()
    {
        return $this->taxesCopy;
    }

    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->order->getIsDraft() ? $this->product->getProduct()->getProductCode() : $this->productCode;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->order->getIsDraft() ? $this->product->getProduct()->getTitle() : $this->productName;
    }

    /**
     * Net price of single product
     *
     * @param bool $with_discount
     * @return mixed
     */
    public function getNetPrice($with_discount = true)
    {
        $price = $this->price;
        if($with_discount){
            $price = round((float)$price * (1-floatval($this->discount)), 2);
        }

        return $price;
    }

    public function getNetTotal($with_discount = true, $applyReturns = false)
    {
        $price = $this->price;
        if($with_discount){
            $price = round((float)$price * (1-floatval($this->discount)), 2);
        }

        $total = $price*$this->quantity;

        if($applyReturns) {
            foreach ($this->productReturns as $return) {
                /** @var InvoiceProductReturn $return */
                $total -= $return->getQuantity() * $return->getRefundPrice();
            }
        }

        return $total;
    }

    public function getDiscountTotal(){
        return $this->getNetTotal(false) - $this->getNetTotal();
    }

    public function getTaxesTotal($net_total = null)
    {
        return $this->getTaxesTotals($net_total)['total'];
    }

    public function getTaxesTotals($net_total = null)
    {
        if($net_total === null){
            $net_total = $this->getNetTotal();
        }

        $taxes = [
            'total' => 0
        ];

        $taxes_entities = $this->order->getIsDraft() && $this->order->getCorrectionOf() === null ? $this->taxes : $this->taxesCopy;
        foreach($taxes_entities as $tax){
            /** @var Taxation $tax */
            $tax_value = round($net_total*$tax->getRate(), 2);
            $taxes['total'] += $tax_value;

            if(!isset($taxes[$tax->getId()])){
                $taxes[$tax->getId()] = $tax_value;
            } else {
                $taxes[$tax->getId()] += $tax_value;
            }
        }

        return $taxes;
    }

    public function getGrossTotal()
    {
        $price = $this->getNetTotal(true);
        $taxes = $this->getTaxesTotal($price);

        return $price + $taxes;
    }

    public function lockForInvoice()
    {
        $base_product = $this->product->getProduct();
        $this->productCode = $base_product->getProductCode();
        $this->productName = $base_product->getTitle();

        $taxes_copies = $this->order->getVendorCompanyCopy()->getTaxation();
        $taxes_copies_by_original_ids = [];
        foreach($taxes_copies as $tax_copy){
            /** @var TaxationCopy $tax_copy */
            $taxes_copies_by_original_ids[$tax_copy->getOriginalTaxation()->getId()] = $tax_copy;
        }

        foreach($this->taxes as $tax){
            $this->taxesCopy[] = $taxes_copies_by_original_ids[$tax->getId()];
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getProductReturns()
    {
        return $this->productReturns;
    }

    /**
     * @param ArrayCollection $productReturns
     */
    public function setProductReturns($productReturns)
    {
        $this->productReturns = $productReturns;
    }

    public function addProductReturn(InvoiceProductReturn $productReturn)
    {
        $productReturn->setInvoiceProduct($this);
        $this->productReturns->add($productReturn);
    }

    public function removeProductReturn(InvoiceProductReturn $productReturn)
    {
        $this->productReturns->removeElement($productReturn);
    }

    /**
     * Gets returned or refunded quantity
     *
     * @
     */
    public function getReturnedQuantity()
    {
        $returned = 0;

        foreach($this->productReturns as $return){
            /** @var InvoiceProductReturn $return */
            $returned += $return->getQuantity();
        }

        return $returned;
    }

    /**
     * Checks if all items are returned or refunded
     *
     * @return bool
     */
    public function isFullyReturned()
    {
        return $this->quantity == $this->getReturnedQuantity();
    }

    public function areReturnsValid(ExecutionContextInterface $context)
    {
        $returnedQuantity = $this->getReturnedQuantity();
        if($returnedQuantity > $this->quantity){
            $context->addViolationAt('returns', 'returns_number_exceed_bought_items_number', ['%current%' => $returnedQuantity, '%max%' => $this->quantity, '%product%' => (string)$this->product->getProduct()]);
        }
    }

    function __clone()
    {
        if($this->id !== null){
            $this->id = null;
            $this->order = null;
            $this->quantity -= $this->getReturnedQuantity();
            $this->productReturns = new ArrayCollection();

            $this->taxes = new ArrayCollection($this->taxes->toArray());
            $this->taxesCopy = new ArrayCollection($this->taxesCopy->toArray());
        }
    }


}
