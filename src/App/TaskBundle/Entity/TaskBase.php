<?php

namespace App\TaskBundle\Entity;

use App\PlaceBundle\Entity\Place;
use App\ProjectBundle\Entity\Category;
use App\StatusBundle\Entity\StatusesProviderInterface;
use App\TaxBundle\Entity\Taxation;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BaseTask
 * @ORM\MappedSuperclass
 */
abstract class TaskBase implements StatusesProviderInterface
{

    const STATUS_NEW = 0;
    const STATUS_INPROGRESS = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_FEEDBACK = 3;
    const STATUS_CLOSED = 4;
    const STATUS_CANCELLED = 5;
    const STATUS_GROUP_NAME = 'task_status';

    const COST_TYPE_QUANTITY = 0;
    const COST_TYPE_FIXED = 1;
    const COST_TYPE_SURFACE = 2;
    const COST_TYPE_TIME = 3;
    const COST_TYPES_GROUP_NAME = 'task_cost_types';

    const TYPE_FREE = 0;
    const TYPE_PAYABLE = 1;
    const TYPE_ADJUSTMENT = 2;
    const TYPES_GROUP_NAME = 'task_types';

    const DEPENDENCY_FINISH_TO_START = 0;
    const DEPENDENCY_FINISH_TO_FINISH = 1;
    const DEPENDENCY_START_TO_START = 2;
//    const DEPENDENCY_START_TO_FINISH = 3; //not supported by dhtmlx Gantt chart
    const DEPENDENCY_GROUP_NAME = 'task_dependencies';

    protected static $statuses = [
        self::STATUS_NEW => 'new',
        self::STATUS_INPROGRESS => 'inprogress',
        self::STATUS_RESOLVED => 'resolved',
        self::STATUS_FEEDBACK => 'feedback',
        self::STATUS_CLOSED => 'closed',
        self::STATUS_CANCELLED => 'cancelled'
    ];

    protected static $costTypes = [
        self::COST_TYPE_FIXED => 'fixed',
        self::COST_TYPE_QUANTITY => 'quantity',
        self::COST_TYPE_SURFACE => 'surface',
        self::COST_TYPE_TIME => 'time'
    ];

    protected static $types = [
        self::TYPE_FREE => 'free',
        self::TYPE_PAYABLE => 'payable',
        self::TYPE_ADJUSTMENT => 'adjustment'
    ];

    protected static $dependencies = [
        self::DEPENDENCY_FINISH_TO_START => 'finish_to_start',
        self::DEPENDENCY_FINISH_TO_FINISH => 'finish_to_finish',
        self::DEPENDENCY_START_TO_START => 'start_to_start',
//        self::DEPENDENCY_START_TO_FINISH => 'start_to_finish' //not supported by dhtmlx Gantt chart
    ];


    public static function getCostTypes()
    {
        return self::$costTypes;
    }

    public static function getTypes()
    {
        return self::$types;
    }

    public static function getDependencies()
    {
        return self::$dependencies;
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
            self::COST_TYPES_GROUP_NAME => self::$costTypes,
            self::TYPES_GROUP_NAME => self::$types,
            self::STATUS_GROUP_NAME => self::$statuses,
            self::DEPENDENCY_GROUP_NAME => self::$dependencies
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
     * @var integer
     * @ORM\Column(name="dependency", type="integer", nullable=true)
     */
    protected $dependency = null;

    /**
     * @var TaskTracker
     *
     * @ORM\ManyToOne(targetEntity="App\TaskBundle\Entity\TaskTracker")
     * @ORM\JoinColumn(name="tracker_id", referencedColumnName="id", nullable=true)
     */
    protected $tracker;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="task_description", type="text", nullable=true)
     */
    protected $taskDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = null;

    /**
     * @var Category
     * 
     * @ORM\ManyToOne(targetEntity="App\ProjectBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    protected $category;  

    /**
     * @var TaskPriority
     * @ORM\ManyToOne(targetEntity="App\TaskBundle\Entity\TaskPriority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id", nullable=true)
     */
    protected $priority;

    /**
     * @var integer
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @var integer
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

    /**
     * @var integer
     * @ORM\Column(name="cost_type", type="integer")
     */
    protected $costType;

    /**
     * @var float
     * @ORM\Column(name="unit_price", type="float")
     */
    protected $unitPrice;

    /**
     * @var float
     * @ORM\Column(name="unit_quantity", type="float")
     */
    protected $unitQuantity = 1;

    /**
     * @var float
     * @ORM\Column(name="profit", type="float", nullable=true)
     */
    protected $profit = 0;


    /**
     * @var integer
     *
     * @Assert\Type(type="integer", message="The lag have to be natural number")
     * @Assert\GreaterThan(value=0, message="The lag have to be greater than 0")
     * @ORM\Column(name="lag", type="integer", nullable=true)
     */
    protected $lag;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_mandatory", type="boolean", nullable=true)
     */
    protected $isMandatory;

    /**
     * @var float
     *
     * @Assert\Type(type="float", message="This field must be numeric")
     * @Assert\GreaterThan(value = 0)
     * @ORM\Column(name="estimated_time", type="float", nullable=true)
     */
    protected $estimatedTime;

    /**
     * @var float
     *
     * @Assert\Type(type="float", message="This field must be numeric")
     * @Assert\GreaterThan(value = 0)
     * @ORM\Column(name="estimated_price", type="float", nullable=true)
     */
    protected $estimatedPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="done_ratio", type="float", options={"default" = 0})
     * @Assert\Range(min=0, max=1, minMessage="This value should be in range of 0% to 100%", maxMessage="This value should be in range of 0% to 100%")
     */
    protected $doneRatio = 0;

    /**
     * @var DateTime
     * @ORM\Column(name="due_date", type="datetime", nullable=true)
     */
    protected $dueDate = null;

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
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $startDate = null;

    /**
     * @var DateTime
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    protected $closedAt = null;

    /**
     * Place in category
     *
     * @var integer
     * @ORM\Column(name="ordering", type="integer")
     * @Assert\GreaterThan(value="0", message="Order have to be greater than 0")
     */
    protected $order;

    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity="App\PlaceBundle\Entity\Place")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     */
    protected $place;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->taxes = new ArrayCollection();
        $this->taxesCopies = new ArrayCollection();
    }

     public function __toString() {
        return $this->name.($this->id !== null ? ' (Id '.$this->id.')' : '');
    }
    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the value of pid
     *
     * @param \App\TaskBundle\Entity\Task $pid
     * @return self
     */
    public function setPid(\App\TaskBundle\Entity\Task $pid)
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * Gets the value of tracker.
     *
     * @return \App\TaskBundle\Entity\TaskTracker 
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    /**
     * Set Tracker
     *
     * @param \App\TaskBundle\Entity\TaskTracker $tracker
     * @return Tracker
     */
    public function setTracker(\App\TaskBundle\Entity\TaskTracker $tracker)
    {
        $this->tracker = $tracker;
        return $this;
    }
    
    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the value of name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets the value of description.
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description = null)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaskDescription()
    {
        return $this->taskDescription;
    }

    /**
     * @param string $taskDescription
     *
     * @return self
     */
    public function setTaskDescription($taskDescription)
    {
        $this->taskDescription = $taskDescription;
        return $this;
    }

    /**
     * Gets the value of dueDate.
     *
     * @return DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Sets the value of dueDate.
     *
     * @param DateTime $dueDate
     *
     * @return self
     */
    public function setDueDate(DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    /**
     * Gets the value of category.
     * 
     * @return \App\ProjectBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Sets category.
     *
     * @param \App\ProjectBundle\Entity\Category $category
     *
     * @return self
     */
    public function setCategory(\App\ProjectBundle\Entity\Category $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Gets the value of priority.
     *
     * @return \App\TaskBundle\Entity\TaskPriority
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     * Sets the value of priority.
     *
     * @param \App\TaskBundle\Entity\TaskPriority $priority
     * @return self
     */
    public function setPriority(\App\TaskBundle\Entity\TaskPriority $priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Gets the value of dependency.
     *
     * @return integer
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * Set Dependency
     *
     * @param integer $dependency
     *
     * @throws \InvalidArgumentException
     * @return Task
     */
    public function setDependency($dependency=null)
    {
        if($dependency !== null && !array_key_exists($dependency, self::$dependencies)){
            throw new \InvalidArgumentException('Dependency "'.$dependency.'" doesn\'t exist');
        }

        $this->dependency = $dependency;
        return $this;
    }

    /**
     * Gets the value of lag.
     *
     * @return integer
     */
    public function getLag()
    {
        return $this->lag;
    }
    
    /**
     * Sets the value of lag.
     *
     * @param integer $lag
     *
     * @return self
     */
    public function setLag($lag)
    {
        if(is_numeric($lag)){
            $lag = intval($lag);
        }
        $this->lag = $lag;
        return $this;
    }

    /**
     * Set isMandatory
     *
     * @param boolean $isMandatory
     * @return Product
     */
    public function setIsMandatory($isMandatory)
    {
        $this->isMandatory = $isMandatory;
        return $this;
    }

    /**
     * Get isMandatory
     *
     * @return boolean
     */
    public function getIsMandatory()
    {
        return $this->isMandatory;
    }

    /**
     * Gets the value of estimatedTime.
     *
     * @return float
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }
    
    /**
     * Sets the value of estimatedTime.
     *
     * @param float $estimatedTime
     * @return self
     */
    public function setEstimatedTime($estimatedTime)
    {
        $this->estimatedTime = $estimatedTime;
        return $this;
    }

    /**
     * @param float $estimatedPrice
     */
    public function setEstimatedPrice($estimatedPrice)
    {
        $this->estimatedPrice = $estimatedPrice;
    }

    /**
     * @return float
     */
    public function getEstimatedPrice()
    {
        return $this->estimatedPrice;
    }



    /**
     * Gets the value of createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * Sets the value of createdAt.
     *
     * @param DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Gets the value of updatedAt.
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * Sets the value of updatedAt.
     *
     * @param DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Gets the value of startDate.
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the value of startDate.
     *
     * @param DateTime $startDate
     *
     * @return self
     */
    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Gets the value of closedAt.
     *
     * @return DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }
    
    /**
     * Sets the value of closedAt.
     *
     * @param DateTime $closedAt
     *
     * @return self
     */
    public function setClosedAt(DateTime $closedAt = null)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Gets the value of doneRatio.
     *
     * @param bool $as_integer
     *
     * @return float|integer
     */
    public function getDoneRatio($as_integer = false)
    {
        return $as_integer === true ? intval($this->doneRatio*100) : $this->doneRatio;
    }
    
    /**
     * Sets the value of doneRatio.
     *
     * @param integer $doneRatio the done ratio
     *
     * @return self
     */
    public function setDoneRatio($doneRatio)
    {
        $this->doneRatio = $doneRatio;

        return $this;
    }

    /**
     * @param float $unitQuantity
     */
    public function setUnitQuantity($unitQuantity)
    {
        $this->unitQuantity = $unitQuantity;
    }

    /**
     * @return float
     */
    public function getUnitQuantity()
    {
        return $this->unitQuantity;
    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

   
    /**
     * @param float $profit
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->profit;
    }

    public function getRealProfit()
    {
        return round($this->profit/(1+$this->profit), 4);
    }

    public function getMarkUp()
    {
        return $this->profit;
    }

    public function getNetPrice($with_profit = true)
    {
        $price = $this->unitPrice !== null ? $this->unitPrice * $this->unitQuantity : 0;
        if($price > 0 && $with_profit) {
            $price += $price*$this->profit;
        }

        return round($price, 2);
    }

    public function getTaxesCostArray($price, $forceCopies = false)
    {
        $taxes_array = [];

        $taxes = $this->taxesLocked || $forceCopies ? $this->taxesCopies : $this->taxes;

        foreach($taxes as $tax){
            /** @var Taxation $tax */
            $taxes_array[$tax->getId()] = round($price * $tax->getRate(), 2);
        }

        return $taxes_array;
    }

    public function getTaxesCost($price = null)
    {
        if($price === null){
            $price = $this->getNetPrice(true);
        }

        return array_sum($this->getTaxesCostArray($price));
    }

    public function getGrossPrice($with_profit = true)
    {
        $price = $this->getNetPrice($with_profit);
        $taxes = $this->getTaxesCost($price);


        return $price + $taxes;
    }

    /**
     * @param Collection|array $taxes
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
    }

    /**
     * @return Collection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * Gets the value of status.
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param integer $status
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setStatus($status)
    {
        if(!isset(self::$statuses[$status])){
            throw new \InvalidArgumentException('Task status "'.$status.'" doesn\'t exist');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Gets the value of taskType.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeName()
    {
        return self::$types[$this->type];
    }

    /**
     * Sets the value of taskType.
     *
     * @param integer $type
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setType($type)
    {
        if(!isset(self::$types[$type])){
            throw new \InvalidArgumentException('Task type "'.$type.'" doesn\'t exist');
        }

        $this->type = $type;

        if($type == self::TYPE_FREE){
            $this->setCostType(self::COST_TYPE_FIXED);
            $this->setUnitPrice(0);
            $this->setProfit(0);
        }

        return $this;
    }

    /**
     * @param mixed $costType
     */
    public function setCostType($costType)
    {
        $this->costType = $costType;

        if($costType == self::COST_TYPE_FIXED){
            $this->setUnitQuantity(1);
        }
    }

    /**
     * @return mixed
     */
    public function getCostType()
    {
        return $this->costType;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $taxesCopies
     */
    public function setTaxesCopies($taxesCopies)
    {
        $this->taxesCopies = $taxesCopies;
        $this->taxesLocked = true;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxesCopies()
    {
        return $this->taxesCopies;
    }

    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param Place $place
     */
    public function setPlace(Place $place = null)
    {
        $this->place = $place;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
}