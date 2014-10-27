<?php

namespace App\PurchaseBundle\Entity\Purchase;

use App\AccountProductBundle\Entity\AccountProduct;
use App\ProductBundle\Entity\Product;
use App\PurchaseBundle\Entity\Purchase;
use App\TaxBundle\Entity\Taxation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="purchase_item")
 * @ORM\Entity(repositoryClass="App\PurchaseBundle\Entity\Purchase\ItemRepository")
 */
class Item
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    protected $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_received", type="integer")
     */
    protected $quantityReceived = 0;

    /**
     * @var Purchase
     *
     * @ORM\ManyToOne(targetEntity="App\PurchaseBundle\Entity\Purchase")
     * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id", nullable=false)
     */
    protected $purchase;

    /**
     * @var AccountProduct
     *
     * @ORM\ManyToOne(targetEntity="App\AccountProductBundle\Entity\AccountProduct")
     * @ORM\JoinColumn(name="account_product_id", referencedColumnName="id", nullable=false)
     */
    protected $product;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\Taxation")
     * @ORM\JoinTable(name="purchase_item_taxes",
     *      joinColumns={@ORM\JoinColumn(name="purchase_item_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tax_id", referencedColumnName="id")}
     * )
     */
    protected $taxes;

    public function __construct()
    {
        $this->taxes = new ArrayCollection();
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
     * @param integer $quantity
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    
        return $this;
    }

    public function increaseQuantity($quantity)
    {
        $this->quantity += $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Item
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantityReceived
     *
     * @param integer $quantityReceived
     * @return Item
     */
    public function setQuantityReceived($quantityReceived)
    {
        $this->quantityReceived = $quantityReceived;
    
        return $this;
    }

    /**
     * Get quantityReceived
     *
     * @return integer 
     */
    public function getQuantityReceived()
    {
        return $this->quantityReceived;
    }

    /**
     * @param \App\PurchaseBundle\Entity\Purchase $purchase
     */
    public function setPurchase(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return \App\PurchaseBundle\Entity\Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param \App\AccountProductBundle\Entity\AccountProduct $product
     */
    public function setProduct(AccountProduct $product)
    {
        $this->product = $product;
    }

    /**
     * @return \App\AccountProductBundle\Entity\AccountProduct
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $taxes
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    public function getNetTotal()
    {
        return $this->price * $this->quantity;
    }

    public function getTaxesTotal($net_total)
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

        foreach($this->taxes as $tax){
            /** @var Taxation $tax */
            $tax_value = round($net_total*$tax->getTaxType()->getRate(), 2);
            $taxes['total'] += $tax_value;

            if(!isset($taxes[$tax->getId()])){
                $taxes[$tax->getId()] = $tax_value;
            } else {
                $taxes[$tax->getId()] += $tax_value;
            }
        }

        return $taxes;
    }

    public function getTotal()
    {
        $net = $this->getNetTotal();
        $taxes = $this->getTaxesTotal($net);

        return $net + $taxes;
    }
}
