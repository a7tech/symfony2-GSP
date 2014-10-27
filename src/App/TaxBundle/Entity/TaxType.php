<?php

namespace App\TaxBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TaxType
 *
 * @ORM\Table(name="tax_type")
 * @ORM\Entity(repositoryClass="App\TaxBundle\Entity\TaxTypeRepository")
 */
class TaxType
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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float")
     */
    private $rate;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;

    /**
     * @var Province
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\Province")
     * @ORM\JoinColumn(name="province_id", referencedColumnName="id")
     */
    private $province;

    /**
     * @var Region
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @var Taxation
     * @ORM\OneToMany(targetEntity="Taxation", mappedBy="taxType")
     **/

    protected $taxations;

    public function __construct() {
        $this->taxations = new ArrayCollection();
    }

    public function __toString() {

        return $this->getName().' ('.($this->getRate(true)).'%)';
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
     * @return TaxType
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
     * Set description
     *
     * @param string $description
     * @return TaxType
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
     * @param \App\TaxBundle\Entity\Country $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return \App\TaxBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \App\TaxBundle\Entity\Province $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @return \App\TaxBundle\Entity\Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @param bool $as_integer
     * @return float
     */
    public function getRate($as_integer = false)
    {
        return $as_integer ? $this->rate *100 : $this->rate;
    }

    /**
     * @param \App\TaxBundle\Entity\Region $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return \App\TaxBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }


}
