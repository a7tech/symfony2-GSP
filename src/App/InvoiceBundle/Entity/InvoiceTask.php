<?php

namespace App\InvoiceBundle\Entity;

use App\TaskBundle\Entity\Task;
use App\TaxBundle\Entity\TaxationCopy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InvoiceTask
 *
 * @ORM\Table(name="invoice_task")
 * @ORM\Entity(repositoryClass="App\InvoiceBundle\Entity\InvoiceTaskRepository")
 */
class InvoiceTask
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
     * @var float
     * @Assert\Type(type="float", message="Please provide valid number")
     * @Assert\GreaterThan(value=0, message="Value must be greater than 0")
     * @ORM\Column(name="refund", type="float", nullable=true)
     */
    protected $refund;

    /**
     * @var SaleOrder
     * @ORM\ManyToOne(targetEntity="SaleOrder", inversedBy="tasks")
     * @ORM\JoinColumn(name="sale_order_id", referencedColumnName="id", nullable=false)
     */
    protected $order;

    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="App\TaskBundle\Entity\Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false)
     */
    protected $task;

    /**
     * @var string
     *
     * @ORM\Column(name="refund_description", type="text", nullable=true)
     */
    protected $refundDescription;

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
     * Set refund
     *
     * @param float $refund
     * @return InvoiceTask
     */
    public function setRefund($refund)
    {
        $this->refund = $refund;
    
        return $this;
    }

    /**
     * Get refund
     *
     * @return float 
     */
    public function getRefund()
    {
        return $this->refund;
    }

    /**
     * @param Task $task
     */
    public function setTask(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return \App\TaskBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param SaleOrder $order
     */
    public function setOrder(SaleOrder $order)
    {
        $this->order = $order;
    }

    /**
     * @return \App\InvoiceBundle\Entity\SaleOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function lockForInvoice()
    {
        $task = $this->getTask();

        $taxes_copies = $this->order->getVendorCompanyCopy()->getTaxation();
        $taxes_copies_by_original_ids = [];
        foreach($taxes_copies as $tax_copy){
            /** @var TaxationCopy $tax_copy */
            $taxes_copies_by_original_ids[$tax_copy->getOriginalTaxation()->getId()] = $tax_copy;
        }

        $taxes_copies = [];
        foreach($task->getTaxes() as $tax){
            $taxes_copies[] = $taxes_copies_by_original_ids[$tax->getId()];
        }

        $task->setTaxesCopies($taxes_copies);
    }

    /**
     * @return string
     */
    public function getRefundDescription()
    {
        return $this->refundDescription;
    }

    /**
     * @param string $description
     */
    public function setRefundDescription($description)
    {
        $this->refundDescription = $description;
    }

    public function getNetPrice($withProfit = true, $currentPrice = true)
    {
        if (!$this->order->isCredit()){
                $price = $this->task->getNetPrice($withProfit);

            if ($currentPrice === true) {
                $price -= $this->refund;
            }
        } else {
            $price = -1*$this->refund;
        }

        return $price;
    }

    public function __clone()
    {
        if($this->id !== null){
            $this->id = null;
            $this->refund = null;
        }
    }
}
