<?php

namespace App\ProjectBundle\Entity;

use App\AccountBundle\Entity\AccountProfile;
use App\CategoryBundle\Entity\CategoryRepository;
use App\InvoiceBundle\Entity\SaleOrder;
use App\TaskBundle\Entity\Task;
use App\PersonBundle\Entity\Person;
use App\UserBundle\Entity\User;
use App\TaxBundle\Entity\Taxation;
use DateTime;
use App\StatusBundle\Entity\StatusesProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="App\ProjectBundle\Entity\ProjectRepository")
 * @Assert\Callback(methods={"_isEndTimeValid"})
 * @Assert\Callback(methods={"_areWorkingDaysValid"})
 */
class Project implements StatusesProviderInterface
{
    const STATUS_TO_PRODUCE = 0;
    const STATUS_STARTED = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FINALIZED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_GROUP_NAME = 'project_status';

    const TYPE_PROJECT = 0;
    const TYPE_ESTIMATE = 1;
    const TYPE_GROUP_NAME = 'project_types';

    protected static $projectStatus = [
        self::STATUS_TO_PRODUCE => 'toproduce',
        self::STATUS_STARTED => 'started',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_FINALIZED => 'finalized',
        self::STATUS_CANCELED => 'canceled'
    ];

    protected static $projectTypes = [
        self::TYPE_PROJECT => 'project',
        self::TYPE_ESTIMATE => 'estimate',
    ];

    public static function getProjectTypes()
    {
        return self::$projectTypes;
    }

    public static function getProjectStatus()
    {
        return self::$projectStatus;
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
            self::STATUS_GROUP_NAME => self::$projectStatus,
            self::TYPE_GROUP_NAME => self::$projectTypes
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="term_condition", type="text")
     * @Assert\NotBlank
     */
    protected $termCondition;

    /**
     * @var integer
     * @ORM\Column(name="type_id", type="integer")
     */
    protected $type = self::TYPE_ESTIMATE;

    /**
     * @var integer
     * @ORM\Column(name="status_id", type="integer")
     */
    protected $status = self::STATUS_TO_PRODUCE;

    /**
     * @var Currency
     * @ORM\ManyToOne(targetEntity="App\CurrencyBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=true)
     */
    protected $currency;

    /**
     * @var AccountProfile
     * @ORM\ManyToOne(targetEntity="App\AccountBundle\Entity\AccountProfile")
     * @ORM\JoinColumn(name="account_profile_id", referencedColumnName="id", nullable=true)
     */
    protected $accountProfile;

    /**
     * @var ProjectOpportunity
     * @ORM\OneToOne(targetEntity="App\ProjectBundle\Entity\ProjectOpportunity")
     * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id", nullable=true)
     */
    protected $opportunity;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\UserBundle\Entity\User", cascade={"all"}, inversedBy="project")
     * @ORM\JoinTable(name="project_members",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $members;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", cascade={"all"})
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     **/
    private $client = null;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", cascade={"all"})
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", nullable=true)
     **/
    private $manager = null;

    /**
     * @var DateTime
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $startDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="end_date_on_last_task", type="boolean")
     */
    protected $endDateOnLastTask;

    /**
     * @var mixed
     *
     * @ORM\Column(name="invoice_delivery_type", type="integer", nullable=true)
     */
    protected $invoiceDeliveryType;
    

    /**
     * @var DateTime
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $endDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="real_start_date", type="datetime", nullable=true)
     */
    protected $realStartDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="real_end_date", type="datetime", nullable=true)
     */
    protected $realEndDate;

    /**
     * @var DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var DateTime
     * @ORM\Column(name="project_date", type="datetime", nullable=true)
     */
    protected $projectDate;

    /**
     * @var float
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid.")
     * @ORM\Column(name="deposit_amount", type="float", nullable=true)
     */
    private $depositAmount;

    /**
     * @var float
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid.")
     * @ORM\Column(name="corrected_deposit_amount", type="float", nullable=true)
     */
    protected $correctedDepositAmount;

    /**
     * @var Array
     *
     * @ORM\Column(name="working_days", type="array")
     */
    protected $workingDays;

    /**
     * var SaleOrder
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\SaleOrder", mappedBy="project")
     */
    protected $invoices;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\TaskBundle\Entity\Task", mappedBy="project")
     * @ORM\OrderBy({"order"="ASC"})
     */
    protected $tasks;

    /**
     * @var SaleOrder
     * @ORM\ManyToOne(targetEntity="App\InvoiceBundle\Entity\SaleOrder")
     * @ORM\JoinColumn(name="deposit_invoice_id", referencedColumnName="id")
     */
    protected $depositInvoice;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\PlaceBundle\Entity\Place")
     * @ORM\JoinTable(name="project_place",
     * 		joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="place_id", referencedColumnName="id")}
     * )
     */
    protected $places;

    /**
     * Holds category structure with tasks [category_id => [category, tasks, children, cost]]
     *
     * @var array
     */
    protected $categoriesStructure;

    /**
     * Start time of working days - using \DateTime allows us to add minutes if required later on
     *
     * @var \DateTime
     * @ORM\Column(name="start_time", type="time")
     */
    protected $startTime;

    /**
     * End time of working days - using \DateTime allows us to add minutes if required later on
     *
     * @var \DateTime
     * @ORM\Column(name="end_time", type="time")
     */
    protected $endTime;

    /**
     * @var float
     *
     * @ORM\Column(name="progress", type="float", options={"default" = 0})
     */
    protected $progress = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->invoices = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->places = new ArrayCollection();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name.($this->id !== null ? ' (Id '.$this->id.')' : '');
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
     * Sets the value of id.
     *
     * @param integer $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of Status.
     *
     * @param integer $status
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setStatus($status)
    {
   
        if(!isset(self::$projectStatus[$status])){
            throw new \InvalidArgumentException('Project status "'.$status.'" doesn\'t exist');
        }

        $this->status = $status;
        return $this;
    }

    /**
     * Gets the value of Status.
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \App\CurrencyBundle\Entity\Currency $currency
     *
     * @return $this
     */
    public function setCurrency(\App\CurrencyBundle\Entity\Currency $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return \App\CurrencyBundle\Entity\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Project
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Project
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return DateTime
     */
    public function getRealStartDate()
    {
        if($this->realStartDate === null){
            return $this->startDate;
        }

        return $this->realStartDate < $this->startDate ? $this->realStartDate : $this->startDate;
    }

    /**
     * @param DateTime $realStartDate
     */
    public function setRealStartDate(\DateTime $realStartDate = null)
    {
        $this->realStartDate = $realStartDate;
    }

    /**
     * @return DateTime
     */
    public function getRealEndDate()
    {
        if($this->realEndDate === null){
            return $this->endDate;
        }

        return $this->realEndDate > $this->endDate ? $this->realEndDate : $this->endDate;
    }

    /**
     * @param DateTime $realEndDate
     */
    public function setRealEndDate(\DateTime $realEndDate = null)
    {
        $this->realEndDate = $realEndDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Project
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Project
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get date of draft became project
     *
     * @return DateTime
     */
    public function getProjectDate()
    {
        return $this->projectDate;
    }

    /**
     * Set date of draft became project
     *
     * @param DateTime $projectDate
     */
    public function setProjectDate($projectDate)
    {
        $this->projectDate = $projectDate;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the value of Type.
     *
     * @param integer $type
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setType($type)
    {
        if(!isset(self::$projectTypes[$type])){
            throw new \InvalidArgumentException('Project type "'.$type.'" doesn\'t exist');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Gets the value of Type.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $invoices
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;
    }

    /**
     * @return mixed
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Gets the value of endDateOnLastTask.
     *
     * @return boolean
     */
    public function getEndDateOnLastTask()
    {
        return $this->endDateOnLastTask;
    }
    
    /**
     * Sets the value of endDateOnLastTask.
     *
     * @param boolean $endDateOnLastTask the end date on last task
     * @return self
     */
    public function setEndDateOnLastTask($endDateOnLastTask)
    {
        $this->endDateOnLastTask = $endDateOnLastTask;
        return $this;
    }

    /**
     * Gets the value of termCondition.
     * @return string
     */
    public function getTermCondition()
    {
        return $this->termCondition;
    }
    
    /**
     * Sets the value of termCondition.
     *
     * @param string $termCondition the term condition
     * @return self
     */
    public function setTermCondition($termCondition)
    {
        $this->termCondition = $termCondition;
        return $this;
    }

    /**
     * Gets the value of accountProfile.
     *
     * @return AccountProfile
     */
    public function getAccountProfile()
    {
        return $this->accountProfile;
    }
    
    /**
     * Sets the value of accountProfile.
     *
     * @param AccountProfile $accountProfile the account profile
     * @return self
     */
    public function setAccountProfile(AccountProfile $accountProfile)
    {
        $this->accountProfile = $accountProfile;
        return $this;
    }

    /**
     * Gets the value of invoiceDeliveryType.
     *
     * @return mixed
     */
    public function getInvoiceDeliveryType()
    {
        return $this->invoiceDeliveryType;
    }
    
    /**
     * Sets the value of invoiceDeliveryType.
     *
     * @param mixed $invoiceDeliveryType the invoice delivery type
     * @return self
     */
    public function setInvoiceDeliveryType($invoiceDeliveryType)
    {
        $this->invoiceDeliveryType = $invoiceDeliveryType;
        return $this;
    }

    /**
     * Gets the value of opportunity.
     *
     * @return \App\ProjectBundle\Entity\ProjectOpportunity
     */
    public function getOpportunity()
    {
        return $this->opportunity;
    }
    
    /**
     * Sets the value of opportunity.
     *
     * @param \App\ProjectBundle\Entity\ProjectOpportunity $opportunity the opportunity
     *
     * @return self
     */
    public function setOpportunity(\App\ProjectBundle\Entity\ProjectOpportunity $opportunity)
    {
        $this->opportunity = $opportunity;

        return $this;
    }

    /**
     * Gets the value of workingDays.
     *
     * @return Array
     */
    public function getWorkingDays()
    {
        return $this->workingDays;
    }
    
    /**
     * Sets the value of workingDays.
     *
     * @param Array $workingDays the working days
     *
     * @return self
     */
    public function setWorkingDays(Array $workingDays)
    {
        $this->workingDays = $workingDays;

        return $this;
    }

    /**
     * Gets the value of depositAmount.
     *
     * @return Integer
     */
    public function getDepositAmount()
    {
        return $this->depositAmount;
    }
    
    /**
     * Sets the value of depositAmount.
     *
     * @param Integer $depositAmount the deposit amount
     *
     * @return self
     */
    public function setDepositAmount($depositAmount)
    {
        $this->depositAmount = $depositAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCorrectedDepositAmount()
    {
        return $this->correctedDepositAmount;
    }

    /**
     * @param float $correctedDepositAmount
     *
     * @return self
     */
    public function setCorrectedDepositAmount($correctedDepositAmount)
    {
        $this->correctedDepositAmount = $correctedDepositAmount;

        return $this;
    }



    /**
     * @return SaleOrder
     */
    public function getDepositInvoice()
    {
        return $this->depositInvoice;
    }

    /**
     * @param SaleOrder $depositInvoice
     */
    public function setDepositInvoice(SaleOrder $depositInvoice = null)
    {
        if($depositInvoice !== null) {
            $depositInvoice->setProject($this);
        }

        $this->depositInvoice = $depositInvoice;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Gets the value of members.
     *
     * @return Collection
     */
    public function getMembers()
    {
        return $this->members;
    }
    
    /**
     * Sets the value of members.
     *
     * @param Collection $members
     * @return self
     */
    public function setMembers($members)
    {
        $this->members = $members;
        return $this;
    }

    public function isEstimate()
    {
        return $this->type === self::TYPE_ESTIMATE;
    }

    public function isProject()
    {
        return !$this->isEstimate();
    }

    /**
     * Return categories with tasks in structure:
     *  [root_category_id] => [
     *      //root specific
     *      [all_tasks] => [],
     *      [first_task] => Task,
     *      [last_task] => Task.
     *      [cost] => [
     *          [net] => float,
     *          [gross] => float,
     *          [taxes] => [
     *              [total] => float,
     *              [tax_id] => float [, [..]]
     *          ]
     *      ]
     *      //all
     *      [category] => Category|ContractCategory,
     *      [tasks] => [Task [, [...]]],
     *      [children] => [Category [, [..]]],
     *      [places] => [
     *          [place] => Place,
     *          [tasks] => [Task [, [...]]]
     *      ],
     *      [without_place] => [Task [, [...]]]
     * ]
     *
     *
     *
     * @param bool          $contracted_tasks
     * @param EntityManager $entity_manager
     * @param bool          $cached
     * @param bool          $from_locked Use locked categories if possible
     * @param bool          $show_cancelled Shows/hides cancelled payable tasks
     *
     * @throws \Exception
     * @return array
     */
    public function getCategories($contracted_tasks = true, EntityManager $entity_manager = null, $cached = true, $from_locked = true, $show_cancelled = true, $allowedIds = null)
    {
        if($this->categoriesStructure === null || $cached === false){
            if($entity_manager === null){
                throw new \Exception('Entity manager cannot be null, while reading not cached result');
            }

            /** @var CategoryRepository $categories_repository */
            $categories_repository = $entity_manager->getRepository('AppProjectBundle:Category');
            $contract_categories_repository = $entity_manager->getRepository('AppProjectBundle:ContractCategory');
            $this->categoriesStructure = [];

            $taxes_template = ['total' => 0];
            foreach($this->getAccountProfile()->getTaxation() as $tax){
                /** @var Taxation $tax */
                $taxes_template[$tax->getId()] = 0;
            }

            foreach($this->tasks as $task){
                /** @var Task $task */
                foreach($task->getTaxesCopies() as $tax_copy){
                    $taxes_template[$tax_copy->getId()] = 0;
                }
            }

            foreach($this->tasks as $task){
                /** @var Task $task */

                if(is_array($allowedIds)){
                    //allow to pass only for allowed tasks
                    if(!in_array($task->getId(), $allowedIds)){
                        continue;
                    }
                }


                if($contracted_tasks === null || ($contracted_tasks === true && $task->isContracted()) || ($contracted_tasks === false && !$task->isContracted())){
                    $add = true;
                    if($show_cancelled === false) {
                        $add = !$task->isCancelled();
                    }

                    if($add){
                        /** @var Task $task */
                        if ($contracted_tasks === true && !$this->isEstimate() && $from_locked) {
                            $path = $contract_categories_repository->getPath($task->getContractCategory());
                        } else {
                            $path = $categories_repository->getPath($task->getCategory());
                        }
                        $this->addCategoryToTree($this->categoriesStructure, $path, $task);
                        $root_category_id = $path[0]->getId();

                        if (!isset($this->categoriesStructure[$root_category_id]['all_tasks'])) {
                            $this->categoriesStructure[$root_category_id] = array_merge($this->categoriesStructure[$root_category_id], [
                                'all_tasks'  => [],
                                'first_task' => null,
                                'last_task'  => null
                            ]);
                        }
                        $this->categoriesStructure[$root_category_id]['all_tasks'][] = $task;
                        /** @var Task $first_task */
                        $first_task = $this->categoriesStructure[$root_category_id]['first_task'];
                        if ($first_task === null || $first_task->getStartDate() > $task->getStartDate()) {
                            $this->categoriesStructure[$root_category_id]['first_task'] = $first_task = $task;
                        }

                        /** @var Task $last_task */
                        $last_task = $this->categoriesStructure[$root_category_id]['last_task'];
                        if ($last_task === null || $last_task->getDueDate() < $task->getDueDate()) {
                            $this->categoriesStructure[$root_category_id]['last_task'] = $last_task = $task;
                        }


                        if (!isset($this->categoriesStructure[$root_category_id]['cost'])) {
                            $this->categoriesStructure[$root_category_id]['cost'] = [
                                'net'            => 0,
                                'gross'          => 0,
                                'due'            => 0,
                                'due_with_taxes' => 0,
                                'deposit'        => 0,
                                'taxes'          => $taxes_template,
                            ];
                        }


                        $main_category = $this->categoriesStructure[$path[0]->getId()];

                        $task_net_price = $task->getNetPrice();
                        $main_category['cost']['net'] += $task_net_price;
                        $taxes_array = $task->getTaxesCostArray($task_net_price);
                        foreach ($taxes_array as $tax_id => $tax) {
                            $main_category['cost']['taxes'][$tax_id] += $tax;
                        }
                        $main_category['cost']['taxes']['total'] += array_sum($taxes_array);
                        $main_category['cost']['gross']          = $main_category['cost']['net'] + $main_category['cost']['taxes']['total'];
                        $main_category['cost']['deposit']        = $main_category['cost']['net'] * $task->getProject()->getDepositAmount();
                        $main_category['cost']['due']            = $main_category['cost']['net'] - $main_category['cost']['deposit'];
                        $main_category['cost']['due_with_taxes'] = $main_category['cost']['due'];
                        $taxes_due_array                         = $task->getTaxesCostArray($main_category['cost']['due']);

                        foreach ($taxes_due_array as $tax_id => $tax) {
                            $main_category['cost']['due_with_taxes'] += $tax;
                        }

                        $this->categoriesStructure[$path[0]->getId()] = $main_category;
                    }
                }
            }

            //sort categories
            $this->sortCategories($this->categoriesStructure);
        }
        
        return $this->categoriesStructure;
    }

    protected function addCategoryToTree(&$structure, $path, Task $task)
    {
        if(count($path) > 0){
            $root = array_shift($path);
            if(!isset($structure[$root->getId()])){
                $structure[$root->getId()] = [
                    'category' => $root,
                    'tasks' => [],
                    'children' => [],
                    'places' => [],
                    'without_place' => []
                ];
            }

            if(count($path) > 0){
                $this->addCategoryToTree($structure[$root->getId()]['children'], $path, $task);
            } else {
                $structure[$root->getId()]['tasks'][] = $task;

                $place = $task->getPlace();
                if($place !== null){
                    if(!isset($structure[$root->getId()]['places'][$place->getId()])) {
                        $structure[$root->getId()]['places'][$place->getId()] = [
                            'place' => $place,
                            'tasks' => []
                        ];
                    }

                    $structure[$root->getId()]['places'][$place->getId()]['tasks'][] = $task;
                } else {
                    $structure[$root->getId()]['without_place'][] = $task;
                }
            }

        }
    }

    protected function sortCategories(&$categories)
    {
        $sorted = uasort($categories, function($a, $b){
            /** @var Category $a_category */
            $a_category = $a['category'];
            /** @var Category $b_category */
            $b_category = $b['category'];

            if($a_category->getLeftValue() == $b_category->getLeftValue()){
                return 0;
            } else {
                return $a_category->getLeftValue() < $b_category->getLeftValue() ? -1 : 1;
            }
        });

        foreach($categories as &$category){
            if(count($category['children']) > 0){
                $this->sortCategories($category['children']);
            }
        }
    }

    public function getContractNetCost(EntityManager $entityManager = null, $cached = true)
    {
        $categories = $this->getCategories(true, $entityManager, $cached);

        $net_total = 0;
        foreach($categories as $category){
            $net_total += $category['cost']['net'];
        }

        return $net_total;
    }

    /**
     * Gets the value of client.
     *
     * @return User
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Sets the value of client.
     *
     * @param User $client the client
     *
     * @return self
     */
    public function setClient(User $client = null )
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Gets the value of manager.
     *
     * @return User
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    /**
     * Sets the value of manager.
     *
     * @param User $manager the manager
     *
     * @return self
     */
    public function setManager(\App\UserBundle\Entity\User $manager = null )
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param ArrayCollection $places
     */
    public function setPlaces($places)
    {
        $this->places = $places;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @param bool $asInt
     *
     * @return int
     */
    public function getProgress($asInt = false)
    {
        return $asInt ? round($this->progress*100) : $this->progress;
    }

    public function setProgress($progress)
    {
        $this->progress = $progress;
    }



    public function getNameWithId()
    {
        return $this->name.' (Id '.$this->id.')';
    }

    public function _isEndTimeValid(ExecutionContextInterface $context)
    {
        if($this->startTime >= $this->endTime){
            $context->addViolationAt('endTime', 'End time have to be greater than start time');
        }
    }

    public function _areWorkingDaysValid(ExecutionContextInterface $context)
    {
        if(count($this->workingDays) == 0){
            $context->addViolationAt('workingDays', 'Please select at least one working day');
        }
    }

    public function getWorkingHours()
    {
        $working_hours = ($this->startTime !== null && $this->endTime !== null) ? $this->endTime->diff($this->startTime)->h : null;
        if($working_hours == 0){
            $working_hours = null;
        }

        return $working_hours;
    }

    public function getStartHourAsInt()
    {
        return $this->startTime !== null ? intval($this->startTime->format('H')) : null;
    }

    public function getEndHourAsInt()
    {
        return $this->endTime !== null ? intval($this->endTime->format('H')) : null;
    }

    public function hasContractedRealInvoices()
    {
        foreach($this->invoices as $invoice){
            /** @var SaleOrder $invoice */
            if(!$invoice->isDepositInvoice() && !$invoice->getIsDraft()){
                return true;
            }
        }

        return false;
    }
}