<?php

namespace App\StatusBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * StatusGroup
 *
 * @ORM\Table(name="status_group")
 * @ORM\Entity(repositoryClass="App\StatusBundle\Entity\GroupRepository")
 * @UniqueEntity("className")
 */
class Group
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=512)
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column(name="class_name", type="string", length=50, unique=true)
     */
    protected $className;

    /**
     * @var ArrayCollection::
     *
     * @ORM\OneToMany(targetEntity="Status", mappedBy="group", cascade="all")
     * @ORM\OrderBy({"order"="ASC"})
     */
    protected $statuses;

    public function __construct($class, $className)
    {
        $this->statuses = new ArrayCollection();
        $this->setClass($class);
        $this->setClassName($className);
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
     * Set name
     *
     * @param string $name
     * @return Group
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
     * Set class
     *
     * @param string $class
     * @return Group
     */
    public function setClass($class)
    {
        $this->class = $class;
    
        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;

        if($this->name === null){
            $this->setName(ucwords(str_replace(['_', '-'], ' ',$className)));
        }
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $statuses
     */
    public function setStatuses($statuses)
    {
        $this->statuses->clear();

        foreach($statuses as $status){
            $this->addStatusItem($status);
        }
    }

    /**
     * @param Status $status
     */
    public function addStatusItem(Status $status)
    {
        $status->setGroup($this);
        $status->setOrder($this->statuses->count());
        $this->statuses->add($status);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    public function __toString()
    {
        return $this->name;
    }
}
