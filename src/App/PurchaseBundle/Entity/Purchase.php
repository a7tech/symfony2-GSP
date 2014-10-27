<?php

namespace App\PurchaseBundle\Entity;

use App\AccountBundle\Entity\AccountProfile;
use App\CompanyBundle\Entity\Company;
use App\PaymentBundle\Entity\Payment;
use App\PurchaseBundle\Entity\Purchase\Item;
use App\StatusBundle\Entity\StatusesProviderInterface;
use App\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Purchase
 *
 * @ORM\Table(name="purchase")
 * @ORM\Entity(repositoryClass="App\PurchaseBundle\Entity\PurchaseRepository")
 */
class Purchase implements StatusesProviderInterface
{
    const STATUS_CANCELED = -1;
    const STATUS_NEW = 1;
    const STATUS_RECEIVING = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_FINALIZED = 4;

    const STATUSES_GROUP_NAME = 'purchase_statuses';

    protected static $statuses = [
        self::STATUS_CANCELED => 'canceled',
        self::STATUS_NEW => 'new',
        self::STATUS_RECEIVING => 'receiving',
        self::STATUS_RECEIVED => 'received',
        self::STATUS_FINALIZED => 'finalized'
    ];

    public static function getStatuses()
    {
        return self::$statuses;
    }

    /**
     * Gets all statuses groups from class
     * Array should be in format [status_group_name => [status_value => status_name, [...]], [...]]
     *
     * @return array
     */
    public static function getStatusesArray()
    {
        return [
            self::STATUSES_GROUP_NAME => self::$statuses
        ];
    }


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * 
     * @ORM\Column(name="status", type="integer")
     */
    protected $status = self::STATUS_NEW;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean")
     */
    protected $isDraft = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    protected $creationDate;

    /**
     * @var float
     *
     * @ORM\Column(name="payable_amount", type="float", nullable=true)
     */
    protected $payableAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_date", type="datetime", nullable=true)
     */
    protected $sentDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="invoice_date", type="datetime", nullable=true)
     */
    protected $invoiceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="edit_date", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $editDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
     */
    protected $creator;

    /**
     * @var AccountProfile
     *
     * @ORM\ManyToOne(targetEntity="App\AccountBundle\Entity\AccountProfile")
     * @ORM\JoinColumn(name="account_profile_id", referencedColumnName="id", nullable=false)
     */
    protected $accountProfile;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\Company")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected $supplier;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\PurchaseBundle\Entity\Purchase\Item", mappedBy="purchase", cascade="all")
     */
    protected $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\PaymentBundle\Entity\Payment", cascade="all")
     * @ORM\JoinTable(name="purchase_payment",
     * 		joinColumns={@ORM\JoinColumn(name="purchase_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="payment_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"maturity"="ASC"})
     */
    protected $payments;

    /**
     * Holds totals
     * LazyLoaded
     *
     * @var array
     */
    protected $totals;

    public function __construct(User $creator)
    {
        $this->creator = $creator;
        $this->creationDate = new \DateTime();
        $this->editDate = clone $this->creationDate;
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
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
     * @param int $status
     * @throws \InvalidArgumentException
     */
    public function setStatus($status)
    {
        if(!isset(self::$statuses[$status])){
            throw new \InvalidArgumentException('Status "'.$status.'" doesn\'t exist');
        }

        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusName()
    {
        return @self::$statuses[$this->status];
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set payableAmmount
     *
     * @param float $payableAmount
     * @return Purchase
     */
    public function setPayableAmount($payableAmount)
    {
        $this->payableAmount = $payableAmount;
    
        return $this;
    }

    /**
     * Get payableAmmount
     *
     * @return float 
     */
    public function getPayableAmount()
    {
        return $this->payableAmount;
    }

    /**
     * Set sentDate
     *
     * @param \DateTime $sentDate
     * @return Purchase
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;
    
        return $this;
    }

    /**
     * Get sentDate
     *
     * @return \DateTime 
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set invoiceDate
     *
     * @param \DateTime $invoiceDate
     * @return Purchase
     */
    public function setInvoiceDate($invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
    
        return $this;
    }

    /**
     * Get invoiceDate
     *
     * @return \DateTime 
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * @return \App\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
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

    /**
     * @return \App\AccountBundle\Entity\AccountProfile
     */
    public function getAccountProfile()
    {
        return $this->accountProfile;
    }

    /**
     * @param \App\AccountBundle\Entity\AccountProfile $accountProfile
     */
    public function setAccountProfile(AccountProfile $accountProfile)
    {
        $this->accountProfile = $accountProfile;
    }

    /**
     * @return \DateTime
     */
    public function getEditDate()
    {
        return $this->editDate;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $items
     */
    public function setItems($items)
    {
        $this->items->clear();
        foreach($items as $item){
            $this->addItemItem($item);
        }
    }

    public function addItemItem(Item $item)
    {
        $item->setPurchase($this);
        $this->items->add($item);
    }

    public function removeItemItem(Item $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Change from draft to order
     */
    public function makeOrder()
    {
        $this->isDraft = false;
        $this->invoiceDate = new \DateTime();
    }

    /**
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    public function resetTotals()
    {
        $this->totals = null;
    }

    public function getTotals($cached = true)
    {
        if($this->totals === null || $cached === false){
            $this->totals = [
                'net' => 0,
                'taxes' => [
                    'total' => 0
                ],
                'total' => 0
            ];

            //add taxes fields
            if($this->getAccountProfile() !== null){
                foreach($this->getAccountProfile()->getTaxation() as $tax){
                    $this->totals['taxes'][$tax->getId()] = 0;
                }
            }

            foreach($this->items as $item){
                /** @var Item $item */
                $net = $item->getNetTotal();
                $taxes = $item->getTaxesTotals($net);

                $this->totals['net'] += $net;
                $this->totals['total'] += $net + $taxes['total'];
                foreach($taxes as $tax_id => $tax){
                    $this->totals['taxes'][$tax_id] += $tax;
                }
            }
        }

        return $this->totals;
    }

    public function getNetTotal()
    {
        return $this->getTotals()['net'];
    }

    public function getTaxes()
    {
        return $this->getTotals()['taxes']['total'];
    }

    public function getTaxesTotals()
    {
        return $this->getTotals()['taxes'];
    }

    public function getTotal()
    {
        return $this->getTotals()['total'];
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    public function getPaid()
    {
        $paid = 0;

        foreach($this->payments as $payment){
            /** @var Payment $payment */
            if($payment->getPaymentDate() !== null){
                $paid += $payment->getAmount();
            }
        }

        return $paid;
    }
}
