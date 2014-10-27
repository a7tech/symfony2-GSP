<?php

namespace App\AddressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="time_zones")
 */
class Timezone
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var Address $address
     * @ORM\OneToMany(targetEntity="Address", mappedBy="timezone", cascade={"persist", "remove"})
     */
    protected $address; 

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;
    
    /**
     * @var string $gmt
     *
     * @ORM\Column(name="gmt", type="string", length=255, nullable=true)
     */
    protected $gmt;

    /**
     * @var ArrayCollection $countries
     * @ORM\ManyToMany(targetEntity="Country", inversedBy="timezones")
     * @ORM\JoinTable(name="countries_timezones")
     */
    protected $countries;


    public function __construct() {
        $this->countries = new ArrayCollection();
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
     * Set address
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }
    
    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set name
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set gmt
     * @param $gmt
     */
    public function setGmt($gmt)
    {
        $this->gmt = $gmt;
    }

    /**
     * Get gmt
     * @return string
     */
    public function getGmt()
    {
        return $this->gmt;
    }
    
    public function __toString()
    {
        return $this->getName().' '.$this->getGmt();
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $countries
     */
    public function setCountries(Collection $countries)
    {
        $this->countries = $countries;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCountries()
    {
        return $this->countries;
    }

}