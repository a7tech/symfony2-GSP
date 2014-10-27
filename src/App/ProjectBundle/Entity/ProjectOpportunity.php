<?php

namespace App\ProjectBundle\Entity;

use App\AccountBundle\Entity\AccountProfile;
use App\CurrencyBundle\Entity\Currency;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project_opportunity")
 * @ORM\Entity(repositoryClass="App\ProjectBundle\Entity\ProjectOpportunityRepository")
 */
class ProjectOpportunity
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
    protected $owner;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="App\PersonBundle\Entity\Person")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    protected $client;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    protected $description;

    /**
     * @var ProjectMilestone
     * @ORM\ManyToOne(targetEntity="App\ProjectBundle\Entity\ProjectMilestone")
     * @ORM\JoinColumn(name="milestone_id", referencedColumnName="id", nullable=true)
     */
    protected $milestone;

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
     * @var DateTime
     * @ORM\Column(name="expected_date", type="datetime", nullable=true)
     */
    protected $expectedDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="commision", type="integer", nullable=true)
     */
    protected $commision;

    /**
     * @var integer
     *
     * @ORM\Column(name="expected_value", type="integer", nullable=true)
     */
    protected $expectedValue;


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
     * @var Project
     * @ORM\OneToOne(targetEntity="Project", mappedBy="opportunity")
     */
    protected $project;

    /**
     * @var float
     * @ORM\Column(name="progress", type="float", options={"default"=0})
     */
    protected $progress = 0;

    /**
     * @param float $progress
     */
    public function setProgress($progress)
    {
        $this->progress = round($progress, 2);
    }

    /**
     * @return float
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name.($this->id !== null ? ' (Id '.$this->id.')' : '');
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
     * @return self
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
     * @param \App\UserBundle\Entity\User $owner
     */
    public function setOwner(\App\UserBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return \App\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param \App\PersonBundle\Entity\Person $client
     */
    public function setClient(\App\PersonBundle\Entity\Person $client)
    {
        $this->client = $client;
    }

    /**
     * @return \App\PersonBundle\Entity\Person
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
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
     * @param \App\CurrencyBundle\Entity\Currency $currency
     * @return self
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
     * Sets the value of milestone.
     *
     * @param \App\projectBundle\Entity\ProjectMilestone $milestone
     * @return self
     */
    public function setMilestone(\App\ProjectBundle\Entity\ProjectMilestone $milestone)
    {
        $this->milestone = $milestone;
        return $this;
    }

    /**
     * Gets the value of milestone.
     *
     * @return \App\projectBundle\Entity\ProjectMilestone
     */
    public function getMilestone()
    {
        return $this->milestone;
    }

    /**
     * Set expectedDate
     *
     * @param \DateTime $expectedDate
     * @return self
     */
    public function setExpectedDate($expectedDate)
    {
        $this->expectedDate = $expectedDate;
    
        return $this;
    }

    /**
     * Get expectedDate
     *
     * @return \DateTime 
     */
    public function getExpectedDate()
    {
        return $this->expectedDate;
    }

    /**
     * Gets the value of lag.
     *
     * @return integer
     */
    public function getCommision()
    {
        return $this->commision;
    }
    
    /**
     * Sets the value of commision.
     *
     * @param integer $commision
     * @return self
     */
    public function setCommision($commision)
    {
        $this->commision = $commision;
        return $this;
    }



    /**
     * Gets the value of expectedValue.
     *
     * @return integer
     */
    public function getExpectedValue()
    {
        return $this->expectedValue;
    }
    
    /**
     * Sets the value of expectedValue.
     *
     * @param integer $expectedValue
     * @return self
     */
    public function setExpectedValue($expectedValue)
    {
        $this->expectedValue = $expectedValue;
        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return self
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
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
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
     * Gets the value of accountProfile.
     *
     * @return App\AccountBundle\Entity\AccountProfile
     */
    public function getAccountProfile()
    {
        return $this->accountProfile;
    }

    /**
     * Sets the value of accountProfile.
     *
     * @param \App\AccountBundle\Entity\AccountProfile $accountProfile the account profile
     * @return self
     */
    public function setAccountProfile(AccountProfile $accountProfile)
    {
        $this->accountProfile = $accountProfile;
        return $this;
    }

    /**
     * @param \App\ProjectBundle\Entity\Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \App\ProjectBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

}