<?php
namespace App\InvoiceBundle\Entity;

use App\InvoiceBundle\Entity\Returns\ProductReturnReason;
use App\InvoiceBundle\Entity\Returns\ReturnType;
use App\TaxBundle\Entity\Taxation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="invoice_products_returns")
 * @Assert\Callback(methods={"isReturnTypeValid", "isRefundPriceValid"})
 */
class InvoiceProductReturn
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
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value=0, message="Value must be greater than 0")
     * @Assert\Type(type="integer", message="Only numeric values allowed")
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @var integer
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0, message="Value must be greater than 0")
     * @Assert\Type(type="integer", message="Only numeric values allowed")
     *
     * @ORM\Column(name="refund_price", type="integer")
     */
    protected $refundPrice;

    /**
     * @var InvoiceProduct
     *
     * @ORM\ManyToOne(targetEntity="InvoiceProduct", inversedBy="productReturns")
     * @ORM\JoinColumn(name="invoice_product_id", referencedColumnName="id", nullable=false)
     */
    protected $invoiceProduct;

    /**
     * @var integer
     *
     */
    protected $returnType;

    /**
     * @var ProductReturnReason
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\InvoiceBundle\Entity\Returns\ProductReturnReason")
     * @ORM\JoinColumn(name="return_reason_id", referencedColumnName="id", nullable=false)
     */
    protected $returnReason;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return InvoiceProduct
     */
    public function getInvoiceProduct()
    {
        return $this->invoiceProduct;
    }

    /**
     * @param InvoiceProduct $invoiceProduct
     */
    public function setInvoiceProduct(InvoiceProduct $invoiceProduct)
    {
        $this->invoiceProduct = $invoiceProduct;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getRefundPrice()
    {
        return $this->refundPrice;
    }

    /**
     * @param int $refundPrice
     */
    public function setRefundPrice($refundPrice)
    {
        $this->refundPrice = $refundPrice;
    }

    /**
     * @return ProductReturnReason
     */
    public function getReturnReason()
    {
        return $this->returnReason;
    }

    /**
     * @param ProductReturnReason $returnReason
     */
    public function setReturnReason($returnReason)
    {
        $this->returnReason = $returnReason;
    }

    /**
     * @return int
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @param int $returnType
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
    }

    public function isReturnTypeValid(ExecutionContextInterface $context)
    {
        if(!in_array($this->returnType, ReturnType::getTypes())){
            $context->addViolationAt('returnType', 'product_return.return_type_not_found');
        }
    }

    public function isRefundPriceValid(ExecutionContextInterface $context)
    {
        if($this->refundPrice > $this->invoiceProduct->getNetPrice(true)){
            $context->addViolationAt('refundPrice', 'Refund price of "'.$this->getInvoiceProduct()->getProduct().'" cannot be bigger than original price');
        }
    }

    public function getNetTotal($with_discount = true)
    {
        return $this->refundPrice*$this->quantity*-1;
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

        $taxes_entities = $this->getInvoiceProduct()->getTaxesCopy();
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
}