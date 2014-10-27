<?php

namespace App\InvoiceBundle\Entity;

use App\AccountBundle\Entity\AccountProfile;
use App\AddressBundle\Entity\Address;
use App\AddressBundle\Entity\AddressCopy;
use App\CompanyBundle\Entity\Company;
use App\CompanyBundle\Entity\CompanyCopy;
use App\CurrencyBundle\Entity\Currency;
use App\InvoiceBundle\Exception\ProductReturnException;
use App\InvoiceBundle\Exception\TaskReturnException;
use App\PaymentBundle\Entity\Payment;
use App\PersonBundle\Entity\Person;
use App\PhoneBundle\Entity\Phone;
use App\ProjectBundle\Entity\Category;
use App\ProjectBundle\Entity\ContractCategory;
use App\ProjectBundle\Entity\Project;
use App\StatusBundle\Entity\StatusesProviderInterface;
use App\TaxBundle\Entity\TaxationCopy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * SaleOrder
 *
 * @ORM\Table(name="invoices")
 * @ORM\Entity(repositoryClass="App\InvoiceBundle\Entity\SaleOrderRepository")
 * @Assert\Callback(methods={"isPaymentValid"})
 */
class SaleOrder implements StatusesProviderInterface
{
    const STATUS_PAID = 2;
    const STATUS_PARTIALLY_PAID = 1;
    const STATUS_UNPAID = 0;
    const STATUS_OVERDUE = -1;
    const STATUS_CANCELLED = -2;
    const STATUSES_GROUP = 'sale_order_payment_statuses';

    const RELATION_SALE_ORDER = 0;
    const RELATION_CONTRACT_DEPOSIT = 1;
    const RELATION_CONTRACT = 2;
    const RELATION_CONTRACT_ADJUSTMENTS = 3;
    const RELATION_CREDIT = 4;

    protected static $statuses = [
        self::STATUS_PAID => 'Paid',
        self::STATUS_PARTIALLY_PAID => 'Partially paid',
        self::STATUS_UNPAID => 'Unpaid',
        self::STATUS_OVERDUE => 'Overdue',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    protected static $relations = [
        self::RELATION_SALE_ORDER => 'sale_order',
        self::RELATION_CONTRACT_DEPOSIT => 'deposit',
        self::RELATION_CONTRACT => 'contract_tasks',
        self::RELATION_CONTRACT_ADJUSTMENTS => 'adjustments',
        self::RELATION_CREDIT => 'credit'
    ];

    public static function getStatuses()
    {
        return self::$statuses;
    }

    public static function getRelations()
    {
        return self::$relations;
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
            self::STATUSES_GROUP => self::$statuses
        ];
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="App\PersonBundle\Entity\Person", inversedBy="customerInvoice")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var string
     * @ORM\Column(name="customer_name", type="string", nullable=true)
     */
    private $customerName;

    /**
     * @var string
     * @ORM\Column(name="client_phone", type="string", nullable=true)
     */
    protected $clientPhone;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\CommonCompany", inversedBy="customerInvoice")
     * @ORM\JoinColumn(name="company_customer_id", referencedColumnName="id")
     */
    private $customerCompany;

    /**
     * @var CompanyCopy
     * @ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\CompanyCopy", cascade="all")
     * @ORM\JoinColumn(name="customer_company_copy_id", referencedColumnName="id")
     */
    protected $customerCompanyCopy;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="App\PersonBundle\Entity\Person", inversedBy="vendorInvoice")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    private $vendor;

    /**
     * @var string
     * @ORM\Column(name="vendor_name", type="string", nullable=true)
     */
    private $vendorName;

    /**
     * @var AccountProfile
     * @ORM\ManyToOne(targetEntity="App\AccountBundle\Entity\AccountProfile", inversedBy="vendorInvoice")
     * @ORM\JoinColumn(name="vendor_company_id", referencedColumnName="id")
     */
    private $vendorCompany;

    /**
     * @var ArrayCollection
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="InvoiceProduct", mappedBy="order", cascade={"all"})
     **/
    private $products;

    /**
     * @var ArrayCollection
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="InvoiceTask", mappedBy="order", cascade="all")
     */
    protected $tasks;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=50, nullable=true )
     */
    private $status = self::STATUS_UNPAID;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\Address")
     * @ORM\JoinColumn(name="shipment_address_id", referencedColumnName="id")
     */
    private $shipment;

    /**
     * @var AddressCopy
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\AddressCopy", cascade="persist")
     * @ORM\JoinColumn(name="shipment_address_copy_id", referencedColumnName="id")
     */
    private $shipmentCopy;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\Address")
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id")
     */
    private $billing;

    /**
     * @var AddressCopy
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\AddressCopy", cascade="persist")
     * @ORM\JoinColumn(name="billing_address_copy_id", referencedColumnName="id")
     */
    private $billingCopy;


    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="invoice_date", nullable=true)
     */
    private $invoiceDate;

    /**
     * @var string
     * @ORM\Column(type="text", name="comment", nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     * @ORM\Column(name="is_rbq", type="boolean", nullable=true)
     */
    private $isRbq;

    /**
     * @var boolean
     * @ORM\Column(name="is_taxable", type="boolean", nullable=true)
     */
    private $isTaxable;
    /**
     * @var boolean
     * @ORM\Column(name="show_delivery_address", type="boolean", nullable=true)
     */
    private $showDeliveryAddress;

    /**
     * @var bool
     * @ORM\Column(name="is_visible", type="boolean")
     */
    protected $isVisible = true;

    /**
     * Amount displayed as deposit paid
     *
     * @var float
     * @ORM\Column(name="deposit_position", type="float", nullable=true)
     */
    protected $depositPosition;

    /**
     * Deposit amount subtracted from total
     *
     * @var float
     * @ORM\Column(name="deposit_return", type="float", nullable=true)
     */
    protected $depositReturn;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\Taxation")
     * @ORM\JoinTable(name="invoice_deposit_tax",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tax_id", referencedColumnName="id")}
     * )
     */
    protected $depositTaxes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\TaxationCopy", cascade={"persist"})
     * @ORM\JoinTable(name="invoice_deposit_tax_copy",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="taxation_copy_id", referencedColumnName="id")}
     * )
     */
    protected $depositTaxesCopies;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="App\ProjectBundle\Entity\Project", inversedBy="invoices")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="App\ProjectBundle\Entity\ContractCategory")
     * @ORM\JoinColumn(name="project_category_id", referencedColumnName="id")
     */
    protected $projectCategory;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="boolean", name="is_draft", nullable=false)
     */
    protected $isDraft = true;

    /**
     * @var ArrayCollection
     * @Assert\Valid
     *
     * @ORM\ManyToMany(targetEntity="App\PaymentBundle\Entity\Payment", cascade="all")
     * @ORM\JoinTable(name="invoices_payment",
     * 		joinColumns={@ORM\JoinColumn(name="invoice_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="payment_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"maturity"="ASC"})
     */
    protected $payments;

    /**
     * @var CompanyCopy
     *
     * @ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\CompanyCopy", cascade="persist")
     * @ORM\JoinColumn(name="company_copy_id", referencedColumnName="id")
     */
    protected $vendorCompanyCopy;

    /**
     * @var SaleOrder
     *
     * @ORM\OneToOne(targetEntity="SaleOrder", mappedBy="correctedBy")
     * @ORM\JoinColumn(name="correction_of_id", referencedColumnName="id")
     *
     * @var SaleOrder
     */
    protected $correctionOf;

    /**
     * @var SaleOrder
     *
     * @ORM\OneToOne(targetEntity="SaleOrder", mappedBy="correctionOf")
     */
    protected $correctedBy;

    /**
     * Store totals (net, taxes, discount, total)
     * LazyLoaded
     *
     * @var array
     */
    protected $totals;

    /**
     * @var Currency
     * 
     * @ORM\ManyToOne(targetEntity="App\CurrencyBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    protected $currency;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_credit", type="boolean", options={"default"=false})
     */
    protected $isCredit = false;

    public function __construct()
    {
        $this->isRbq = true;
        $this->isTaxable = true;
        $this->showDeliveryAddress = true;
        $this->invoiceDate = new \DateTime();
        $this->products = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->depositTaxes = new ArrayCollection();
        $this->depositTaxesCopies = new ArrayCollection();
    }

    public function __toString()
    {
        return '#' . $this->getId();
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
     * @param \App\PersonBundle\Entity\Person $customer
     */
    public function setCustomer(Person $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \App\PersonBundle\Entity\Person
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products->clear();
        foreach ($products as $product) {
            $this->addProductItem($product);
        }


        $this->totals = null;
    }

    public function hasProduct($product)
    {
        return $this->products->contains($product);
    }

    public function hasProducts()
    {

        return !$this->products->isEmpty();
    }

    public function addProductItem(InvoiceProduct $product)
    {

        if (!$this->hasProduct($product)) {
            $product->setOrder($this);
            $this->products->add($product);
            $this->totals = null;
        }
    }

    public function removeProductItem($product)
    {
        if ($this->hasProduct($product)) {
            $this->products->remove($product);
            $this->totals = null;
        }
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $tasks
     */
    public function setTasks($tasks)
    {
        $this->tasks->clear();

        foreach ($tasks as $task) {
            $this->addTaskItem($task);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function getTasksCategories(EntityManager $entity_manager, $cached = true)
    {
        //show categories of only invoiced tasks
        $invoicedTasksIds = [];
        foreach($this->tasks as $invoiceTask){
            $invoicedTasksIds[] = $invoiceTask->getTask()->getId();
        }

        return $this->projectCategory !== null ? $this->project->getCategories(true, $entity_manager, $cached, true, true, $invoicedTasksIds)[$this->projectCategory->getId()] : null;
    }

    public function addTaskItem(InvoiceTask $task)
    {
        if (!$this->tasks->contains($task)) {
            $task->setOrder($this);
            $this->tasks->add($task);
        }
    }

    public function removeTaskItem(InvoiceTask $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * @param string $status
     *
     * @throws \InvalidArgumentException
     */
    public function setStatus($status)
    {
        if (!isset(self::$statuses[$status])) {
            throw new \InvalidArgumentException('Status "' . $status . '" doesn\'t exist');
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \App\PersonBundle\Entity\Person $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return \App\PersonBundle\Entity\Person
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param Company $customerCompany
     */
    public function setCustomerCompany($customerCompany)
    {
        $this->customerCompany = $customerCompany;
    }

    /**
     * @return Company
     */
    public function getCustomerCompany()
    {
        return $this->customerCompany;
    }

    /**
     * @return string
     */
    public function getCustomerCompanyName()
    {
        if ($this->isDraft) {
            return $this->customerCompany !== null ? $this->customerCompany->getName() : null;
        } else {
            return $this->customerCompanyCopy !== null ? $this->customerCompanyCopy->getName() : null;
        }
    }

    /**
     * @param string $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->isDraft) {
            return $this->customer !== null ? $this->getCustomer()->getName() : null;
        } else {
            return $this->customerName;
        }
    }

    /**
     * @param Address $shipment
     */
    public function setShipment($shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * @return Address
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * @return \App\AddressBundle\Entity\AddressCopy
     */
    public function getShipmentCopy()
    {
        return $this->shipmentCopy;
    }

    /**
     * @param AccountProfile $vendorCompany
     */
    public function setVendorCompany($vendorCompany)
    {
        $this->vendorCompany = $vendorCompany;

        if ($this->currency === null) {
            $vendorDefaultCurrency = $vendorCompany->getDefaultCurrency();

            if ($vendorDefaultCurrency !== null) {
                $this->currency = $vendorDefaultCurrency->getCurrency();
            }
        }
    }

    /**
     * @return AccountProfile
     */
    public function getVendorCompany()
    {
        return $this->vendorCompany;
    }

    public function getVendorCompanyCopy()
    {
        return $this->vendorCompanyCopy;
    }

    /**
     * @return string
     */
    public function getVendorCompanyName()
    {
        return $this->isDraft ? $this->vendorCompany->getName() : $this->vendorCompanyCopy->getName();
    }

    /**
     * @param string $vendorName
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     * @return string
     */
    public function getVendorName()
    {
        return $this->isDraft ? $this->vendor->getName() : $this->vendorName;
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(\DateTime $date = null)
    {
        $this->invoiceDate = $date;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param \App\CurrencyBundle\Entity\Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return \App\CurrencyBundle\Entity\Currency
     */
    public function getCurrency()
    {
        if ($this->currency === null && $this->vendorCompany !== null) {
            $vendorDefaultCurrency = $this->vendorCompany->getDefaultCurrency();

            if ($vendorDefaultCurrency !== null) {
                $this->currency = $vendorDefaultCurrency->getCurrency();
            }
        }

        return $this->currency;
    }

    /**
     * @param mixed $billing
     */
    public function setBilling($billing)
    {
        $this->billing = $billing;
    }

    /**
     * @return mixed
     */
    public function getBilling()
    {
        return $this->billing;
    }

    public function getBillingCopy()
    {
        return $this->billingCopy;
    }

    /**
     * @param boolean $isRbq
     */
    public function setIsRbq($isRbq)
    {
        $this->isRbq = $isRbq;
    }

    /**
     * @return boolean
     */
    public function getIsRbq()
    {
        return $this->isRbq;
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
     * @param boolean $showDeliveryAddress
     */
    public function setShowDeliveryAddress($showDeliveryAddress)
    {
        $this->showDeliveryAddress = $showDeliveryAddress;
    }

    /**
     * @return boolean
     */
    public function getShowDeliveryAddress()
    {
        return $this->showDeliveryAddress;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        if ($project !== null) {
            $this->setVendorCompany($project->getAccountProfile());

            $client = $project->getClient();

            if ($client !== null) {
                $person = $client->getPerson();
                $this->setCustomer($person);

                $billing_address = $person->getBillingAddress();
                $this->setBilling($billing_address);
            }
        }
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \App\ProjectBundle\Entity\ContractCategory $projectCategory
     */
    public function setProjectCategory(ContractCategory $projectCategory = null)
    {
        $this->projectCategory = $projectCategory;
    }

    /**
     * @return \App\ProjectBundle\Entity\ContractCategory
     */
    public function getProjectCategory()
    {
        return $this->projectCategory;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param boolean $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @return SaleOrder
     */
    public function getCorrectionOf()
    {
        return $this->correctionOf;
    }

    /**
     * @param SaleOrder $correctionOf
     */
    public function setCorrectionOf(SaleOrder $correctionOf = null)
    {
        $this->correctionOf = $correctionOf;
        $correctionOf->setStatus(self::STATUS_CANCELLED);
    }

    /**
     * @return SaleOrder
     */
    public function getCorrectedBy()
    {
        return $this->correctedBy;
    }

    /**
     * @param SaleOrder $correctedBy
     */
    public function setCorrectedBy(SaleOrder $correctedBy = null)
    {
        if ($correctedBy !== null) {
            $correctedBy->setCorrectionOf($this);
            $this->status = self::STATUS_CANCELLED;
        } else {
            $this->updatePaidStatus();
        }

        $this->correctedBy = $correctedBy;
    }


    public function hasTasksRefunds()
    {
        foreach($this->tasks as $task){
            if($task->getRefund() > 0){
                return true;
            }
        }

        return false;
    }

    /**
     * Makes draft an invoice
     */
    public function makeInvoice()
    {
        $this->isDraft = false;

        if ($this->correctionOf === null) {
            //regular invoice looking
            $this->vendorCompanyCopy = new CompanyCopy($this->vendorCompany);
            if ($this->customerCompany !== null) {
                $this->customerCompanyCopy = new CompanyCopy($this->customerCompany);
            }
            if ($this->customer !== null) {
                $this->customerName = (string)$this->customer;
            }
            $this->vendorName = (string)$this->vendor->getName();
            if ($this->shipment !== null) {
                $this->shipmentCopy = new AddressCopy($this->shipment);
            }
            $this->billingCopy = new AddressCopy($this->billing);
            $this->clientPhone = $this->getClientPhone();

            //lock products
            foreach ($this->products as $product) {
                /** @var InvoiceProduct $product */
                $product->lockForInvoice();
            }

            //lock tasks
            foreach ($this->tasks as $task) {
                /** @var InvoiceTask $task */
                $task->lockForInvoice();
            }

            //lock deposit taxes
            if ($this->isDepositInvoice()) {
                $taxes_copies = $this->vendorCompanyCopy->getTaxation();
                $taxes_copies_by_original_ids = [];
                foreach ($taxes_copies as $tax_copy) {
                    /** @var TaxationCopy $tax_copy */
                    $taxes_copies_by_original_ids[$tax_copy->getOriginalTaxation()->getId()] = $tax_copy;
                }

                $taxes_copies = [];
                foreach ($this->depositTaxes as $tax) {
                    $taxes_copies[] = $taxes_copies_by_original_ids[$tax->getId()];
                }

                $this->setDepositTaxesCopies(new ArrayCollection($taxes_copies));
            }
        } else {
            //return invoice locking
            //everything was copied in __clone - no need for additional logic
        }
    }

    /**
     * @return string
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    public function isDepositInvoice()
    {
        return $this->depositPosition !== null;
    }

    public function isContractedInvoice()
    {
        return $this->project !== null && $this->projectCategory !== null;
    }

    /**
     * @return float
     */
    public function getDepositReturn()
    {
        return $this->depositReturn;
    }

    /**
     * @param float $depositReturn
     */
    public function setDepositReturn($depositReturn)
    {
        $this->depositReturn = round($depositReturn, 2);
    }

    /**
     * @return float
     */
    public function getDepositPosition()
    {
        return $this->depositPosition;
    }

    /**
     * @param float $depositPosition
     */
    public function setDepositPosition($depositPosition)
    {
        $this->depositPosition = round($depositPosition, 2);
    }

    /**
     * @return boolean
     */
    public function getIsCredit()
    {
        return $this->isCredit;
    }

    /**
     * @param boolean $isCredit
     */
    public function setIsCredit($isCredit)
    {
        $this->isCredit = $isCredit;
    }

    /**
     * Alias for getIsCredit
     *
     * @return bool
     */
    public function isCredit()
    {
        return $this->getIsCredit();
    }

    /**
     * @return ArrayCollection
     */
    public function getDepositTaxesCopies()
    {
        return $this->depositTaxesCopies;
    }

    /**
     * @param ArrayCollection $depositTaxesCopies
     */
    public function setDepositTaxesCopies($depositTaxesCopies)
    {
        $this->depositTaxesCopies = $depositTaxesCopies;
    }

    /**
     * @return ArrayCollection
     */
    public function getDepositTaxes()
    {
        return $this->depositTaxes;
    }

    /**
     * @param ArrayCollection $depositTaxes
     */
    public function setDepositTaxes($depositTaxes)
    {
        $this->depositTaxes = $depositTaxes;
    }

    public function getDepositTaxesTotals($current = true)
    {
        $taxes = $this->getIsDraft() && $this->getCorrectionOf() === null ? $this->getDepositTaxes() : $this->getDepositTaxesCopies();

        $taxes_array = [
            'total' => 0
        ];

        $depositPosition = ($current ? $this->depositPosition : $this->getCorrectionOf()->getDepositPosition());

        foreach ($taxes as $tax) {
            /** @var $tax TaxationCopy */
            $tax_amount = round($depositPosition * $tax->getRate(), 2);
            $taxes_array['total'] += $tax_amount;

            if (!isset($taxes_array[$tax->getId()])) {
                $taxes_array[$tax->getId()] = $tax_amount;
            } else {
                $taxes_array[$tax->getId()] += $tax_amount;
            }
        }

        return $taxes_array;
    }

    public function getDepositReturnTotal()
    {
        if ($this->depositReturn !== null) {
            $taxes = 0;

            $taxes_entities = null;
            if ($this->isDraft) {
                if ($this->depositTaxesCopies->count() > 0 || $this->getCorrectionOf() !== null) {
                    $taxes_entities = $this->depositTaxesCopies;
                } else {
                    $taxes_entities = $this->project->getDepositInvoice()->getDepositTaxes();
                }

            } else {
                $taxes_entities = $this->depositTaxesCopies;
            }

            foreach ($taxes_entities as $tax) {
                $taxes += round($this->depositReturn * $tax->getRate(), 2);
            }

            return $this->depositReturn + $taxes;
        } else {
            return null;
        }
    }

    public function getDepositTaxesTotal()
    {
        return $this->getDepositTaxesTotals()['total'];
    }

    public function getNetTotal($with_discount = true)
    {
        return $with_discount ? $this->getTotals()['net'] : $this->getTotals()['net_without_discount'];
    }

    public function getDiscount()
    {
        return $this->getTotals()['discount'];
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

    public function getTotals($current = true)
    {
        $intCurrent = (int)$current;

        if (!isset($this->totals[$intCurrent])) {
            $totals = [
                'net_without_discount' => 0,
                'net' => 0,
                'discount' => 0,
                'taxes' => [
                    'total' => 0
                ],
                'total' => 0,
                'products' => [
                    'net_without_discount' => 0,
                    'net' => 0,
                    'discount' => 0,
                    'taxes' => [
                        'total' => 0
                    ],
                    'total' => 0
                ],
                'tasks' => [
                    'net' => 0,
                    'taxes' => [
                        'total' => 0
                    ],
                    'total' => 0,
                ]
            ];


            $vendorCompany = $this->isDraft && $this->getCorrectionOf() === null ? $this->getVendorCompany() : $this->vendorCompanyCopy;

            //deposit
            if ($this->isDepositInvoice()) {
                $totals['net_without_discount'] += ($current ? $this->depositPosition : $this->getCorrectionOf()->getDepositPosition());
                $totals['net'] = $totals['net_without_discount'];
                $totals['taxes'] = $this->getDepositTaxesTotals($current);
                $totals['total'] = $totals['net_without_discount'] + $totals['taxes']['total'];
            } else {
                if ($vendorCompany !== null && !$this->isCredit()) {
                    //add taxes fields
                    foreach ($vendorCompany->getTaxation() as $tax) {
                        $totals['taxes'][$tax->getId()] = 0;
                        $totals['products']['taxes'][$tax->getId()] = 0;
                        $totals['tasks']['taxes'][$tax->getId()] = 0;
                    }
                }

                foreach ($this->products as $product) {
                    /** @var InvoiceProduct $product */
                    $price_without_discount = $product->getNetTotal(false, $current);
                    $price = $product->getNetTotal(true, $current);
                    $discount = $price_without_discount - $price;
                    $taxes = $product->getTaxesTotals($price);
                    $total = $price + $taxes['total'];

                    $totals['net_without_discount'] += $price_without_discount;
                    $totals['net'] += $price;
                    $totals['discount'] += $discount;
                    $totals['total'] += $total;

                    foreach ($taxes as $tax_id => $tax) {
                        $totals['taxes'][$tax_id] += $tax;
                    }

                    $totals['products']['net_without_discount'] += $price_without_discount;
                    $totals['products']['net'] += $price;
                    $totals['products']['discount'] += $discount;
                    $totals['products']['total'] += $total;

                    foreach ($taxes as $tax_id => $tax) {
                        $totals['products']['taxes'][$tax_id] += $tax;
                    }
                }

                foreach ($this->tasks as $invoice_task) {
                    /** @var InvoiceTask $invoice_task */
                    $task = $invoice_task->getTask();
                    $price = $invoice_task->getNetPrice(true, $current);
                    $taxes = $task->getTaxesCostArray($price, $this->getCorrectionOf() !== null);
                    $taxes_total = array_sum($taxes);
                    $total       = $price + $taxes_total;

                    $totals['net_without_discount'] += $price;
                    $totals['net'] += $price;
                    $totals['total'] += $total;
                    $totals['taxes']['total'] += $taxes_total;

                    if(!$this->isCredit()){
                        foreach ($taxes as $tax_id => $tax) {
                            $totals['taxes'][$tax_id] += $tax;
                        }
                    }

                    $totals['tasks']['net'] += $price;
                    $totals['tasks']['total'] += $total;
                    $totals['tasks']['taxes']['total'] += $taxes_total;

                    if(!$this->isCredit()) {
                        foreach ($taxes as $tax_id => $tax) {
                            $totals['tasks']['taxes'][$tax_id] += $tax;
                        }
                    }
                }

                $total_deposit_return = $this->getDepositReturnTotal();
                $totals['total_before_deposit'] = $totals['total'];
                if ($total_deposit_return !== null) {
                    $totals['total'] -= $total_deposit_return;
                }

            }

            $this->totals[$intCurrent] = $totals;
        }

        return $this->totals[$intCurrent];
    }

    public function getReturnTotalsDifference()
    {
        $oldTotals = $this->getTotals(false);
        $newTotals = $this->getTotals(true);

        $this->subtractTotals($oldTotals, $newTotals);

        return $oldTotals;
    }

    protected function subtractTotals(&$base, $minus)
    {
        foreach ($base as $key => &$value) {
            if (is_array($value)) {
                $this->subtractTotals($value, $minus[$key]);
            } else {
                $value -= $minus[$key];
            }
        }
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;

        $this->updatePaidStatus();
    }

    protected function updatePaidStatus()
    {
        $paid = $this->getPaid();

        if ($this->isPaid()) {
            $this->setStatus(self::STATUS_PAID);
        } elseif ($paid == 0) {
            $this->setStatus(self::STATUS_UNPAID);
        } else {
            $this->setStatus(self::STATUS_PARTIALLY_PAID);
        }
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

        foreach ($this->payments as $payment) {
            /** @var Payment $payment */
            if ($payment->getPaymentDate() !== null) {
                $paid += $payment->getAmount();
            }
        }

        return $paid;
    }

    public function isPaid()
    {
        $paid = $this->getPaid();
        $total = round($this->getTotal(), 2);
        $epsilon = 0.00001;

        return $paid <= $total + $epsilon && $paid >= $total - $epsilon;
    }

    public function getScheduledPayment()
    {
        $paid = 0;

        foreach ($this->payments as $payment) {
            /** @var Payment $payment */
            $paid += $payment->getAmount();
        }

        return $paid;
    }

    public function isPaymentValid(ExecutionContextInterface $context)
    {
        //add Epsilon to total to avoid PHP floats comparison bug
        if(!$this->isCredit() && $this->getScheduledPayment() > $this->getTotal() + 0.0000001){
            $context->addViolationAt('payments', 'Payments cannot exceed total amount');
        }
    }

    public function getClientPhone()
    {
        if (!$this->isDraft) {
            return $this->clientPhone;
        } else {
            $phones = $this->customerCompany !== null ? $this->customerCompany->getPhones() : new ArrayCollection();
            if ($phones->count() == 0 && $this->customer !== null) {
                $phones = $this->customer->getPhones();
            }

            if ($phones->count() > 0) {
                /** @var Phone $phone */
                $phone = $phones[0];
                $prefix = $phone->getPhoneIsoCode();
                return ($prefix !== null ? '+' . $prefix->getPrefix() : '') . ' ' . $phone->getNumber() . ' ' . $phone->getExtension();
            } else {
                return null;
            }
        }
    }

    public function getRelation()
    {
        if ($this->project !== null) {
            if ($this->projectCategory !== null) {
                return self::RELATION_CONTRACT;
            } elseif($this->isCredit) {
                return self::RELATION_CREDIT;
            } else {
                if ($this->depositPosition !== null) {
                    return self::RELATION_CONTRACT_DEPOSIT;
                } else {
                    return self::RELATION_CONTRACT_ADJUSTMENTS;
                }
            }
        } else {
            return self::RELATION_SALE_ORDER;
        }
    }

    public function getRelationName()
    {
        return self::$relations[$this->getRelation()];
    }

    public function addProductReturn(InvoiceProductReturn $productReturn)
    {
        $added = false;

        foreach ($this->products as $invoiceProduct) {
            /** @var InvoiceProduct $invoiceProduct */
            if($invoiceProduct->getProduct()->getId() === $productReturn->getInvoiceProduct()->getProduct()->getId()){
                $invoiceProduct->addProductReturn($productReturn);
                $added = true;
                break;
            }
        }

        if ($added === false) {
            throw new ProductReturnException('Product not found');
        }
    }

    public function removeProductReturn(InvoiceProductReturn $productReturn)
    {
        $removed = false;

        foreach ($this->products as $invoiceProduct) {
            /** @var InvoiceProduct $invoiceProduct */
            if ($invoiceProduct->getId() == $productReturn->getInvoiceProduct()->getId()) {
                $invoiceProduct->removeProductReturn($productReturn);
                $removed = true;
                break;
            }
        }

        if ($removed === false) {
            throw new ProductReturnException('Product not found');
        }
    }

    public function getProductReturns()
    {
        $returns = [];

        foreach ($this->products as $invoiceProduct) {
            /** @var InvoiceProduct $invoiceProduct */
            $returns = array_merge($returns, $invoiceProduct->getProductReturns()->toArray());
        }

        return $returns;
    }

    public function __clone()
    {
        if($this->id !== null){
            $this->id = null;
            $this->createdAt = new \DateTime();
            $this->updatedAt = null;
            $this->isDraft = true;
            $this->payments->clear();
            $this->correctedBy = null;
            $this->correctionOf = null;
            $this->invoiceDate = null;
            $this->status = self::STATUS_UNPAID;

            //products copies
            $products = $this->products->toArray();
            $this->products->clear();

            foreach($products as $invoiceProduct){
                $this->addProductItem(clone $invoiceProduct);
            }

            //tasks copies
            $tasks = $this->tasks->toArray();
            $this->tasks->clear();

            foreach($tasks as $invoiceTask){
                /** @var InvoiceTask $invoiceTask */
                if ($invoiceTask->getRefund() === null) {
                    $this->addTaskItem(clone $invoiceTask);
                }
            }

            //deposit taxes & deposit taxes copies
            $this->depositTaxesCopies = new ArrayCollection($this->depositTaxesCopies->toArray());
            $this->depositTaxes = new ArrayCollection($this->depositTaxes->toArray());
        }
    }
}
