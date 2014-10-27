<?php

namespace App\AddressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToBooleanArrayTransformer;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity
 */
class Address
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
     * Address Type.
     * @ORM\ManyToOne(targetEntity="AddressType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="address_type_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $addressType;

    /**
     * Country.
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     * })
     * @var Country
     */
    protected $country;

    /**
     * Province.
     * @ORM\ManyToOne(targetEntity="Province")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="province_id", referencedColumnName="id", nullable=true)
     * })
     * @var Province
     */
    protected $province;

    /**
     * Region..
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="regiony_id", referencedColumnName="id", nullable=true)
     * })
     * @var Region
     */
    protected $region;

    /**
     * City.
     * @ORM\Column(name="city", type="string", nullable=true)
     * @var string
     */
    protected $city;

    /**
     * Postcode.
     * @ORM\Column(name="postcode", type="string", nullable=true)
     * @var string
     */
    protected $postcode;

    /**
     * Street.
     * @ORM\Column(name="street", type="string", nullable=true)
     * @var string
     */
    protected $street;

    /**
     * Building
     * @ORM\Column(name="building", type="string", nullable=true)
     * @var string
     */
    protected $building;

    /**
     * Suite
     * @ORM\Column(name="suite", type="string", nullable=true)
     * @var string
     */
    protected $suite;

    /**
     * @ORM\Column(name="po", type="string", nullable=true)
     * @var string
     */
    protected $po;

    /**
     * Time zone.
     * @ORM\ManyToOne(targetEntity="Timezone", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="timezone_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     * @var Timezone;
     */
    protected $timeZone;

    /**
     * Update time.
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * Creation time.
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime
     */

    protected $createdAt;

    /**
     * @var boolean
     * @ORM\Column(name="is_main", type="boolean")
     */
    protected $isMain;

    /**
     * @var boolean
     * @ORM\Column(name="is_billing", type="boolean", nullable=true)
     */
    protected $isBilling;

    /**
     * @var boolean
     * @ORM\Column(name="is_shipping", type="boolean", nullable=true)
     */
    protected $isShipping;

    /**
     * @var string
     * @ORM\Column(name="contact", type="string", nullable=true)
     */
    protected $contact;

    /**
     * @ORM\ManyToMany(targetEntity="App\CompanyBundle\Entity\CommonCompany", mappedBy="addresses")
     **/
    private $companies;

    /**
     * @ORM\ManyToMany(targetEntity="App\PersonBundle\Entity\Person", mappedBy="addresses")
     **/
    private $persons;


    public function __toString()
    {
        return $this->getFormatted(null, ' ');
    }

    public function getFormatted($suite_label = null, $lineSeparator = '<br>')
    {
        $address = '';

        if ($this->getBuilding()) {
            $address .= $this->getBuilding();
        }

        if ($this->getStreet()) {
            if(strlen($address) > 0){
                $address .= ', ';
            }
            $address .= $this->getStreet();
        }

        if ($this->getSuite()) {
            if(strlen($address) > 0){
                $address .= $lineSeparator;
            }

            $address .= '('.($suite_label !== null ? $suite_label.' ' : '').$this->getSuite().')';
        }

        if(strlen($address) > 0){
            $address .= $lineSeparator;
        }

        if ($this->getCity()) {
            $address .= $this->getCity();
        }
        if ($this->getPostcode()) {
            if(strlen($address) > 0){
                $address .= ', ';
            }

            $address .= $this->getPostcode();
        }
        if ($this->getProvince()) {
            if(strlen($address) > 0){
                $address .= ', ';
            }
            $address .= $this->getProvince();
        }
        if ($this->getCountry()) {
            if(strlen($address) > 0){
                $address .= ', ';
            }
            $address .= $this->getCountry();
        }



        return $address;
    }

    function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->isMain = false;
        $this->isBilling = false;
        $this->isShipping = false;
        $this->companies = new ArrayCollection();
        $this->persons =  new ArrayCollection();
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
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address type
     * @param \App\AddressBundle\Entity\AddressType $addressType
     */
    public function setAddressType(AddressType $addressType)
    {
        $this->addressType = $addressType;
    }

    /**
     * Get Address type
     * @return \App\AddressBundle\Entity\AddressType
     */
    public function getAddressType()
    {
        return $this->addressType;
    }

    /**
     * Set city
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     * @param \App\AddressBundle\Entity\Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     * @return \App\AddressBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set created at
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created at
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set postcode
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $number = str_replace('-', '', $postcode);
        $number = str_replace('.', '', $postcode);
        $number = str_replace(',', '', $postcode);
        $number = str_replace('_', '', $postcode);
        $number = str_replace(' ', '', $postcode);
        $this->postcode = trim($number);
    }

    /**
     * Get postcode
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set province
     * @param \App\AddressBundle\Entity\Province $province
     */
    public function setProvince(Province $province = null)
    {
        $this->province = $province;
    }

    /**
     * Get province
     * @return \App\AddressBundle\Entity\Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set region
     * @param \App\AddressBundle\Entity\Region $region
     */
    public function setRegion(Region $region = null)
    {
        $this->region = $region;
    }

    /**
     * Get region
     * @return \App\AddressBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set updated at
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updated at
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set time zone
     * @param Timezone $timeZone
     */
    public function setTimeZone(Timezone $timeZone)
    {
        $this->timeZone = $timeZone;
    }

    /**
     * Get Timezone
     * @return Timezone
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @param string $building
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $suite
     */
    public function setSuite($suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return string
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * @param string $po
     */
    public function setPo($po)
    {
        $this->po = $po;
    }

    /**
     * @return string
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * @param boolean $isMain
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;
    }

    /**
     * @return boolean
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * @param mixed $companies
     */
    public function setCompanies($companies)
    {
        $this->companies = $companies;
    }

    /**
     * @return mixed
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * @param string $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param boolean $isBilling
     */
    public function setIsBilling($isBilling)
    {
        $this->isBilling = $isBilling;
    }

    /**
     * @return boolean
     */
    public function getIsBilling()
    {
        return $this->isBilling;
    }

    /**
     * @param boolean $isShipping
     */
    public function setIsShipping($isShipping)
    {
        $this->isShipping = $isShipping;
    }

    /**
     * @return boolean
     */
    public function getIsShipping()
    {
        return $this->isShipping;
    }

    /**
     * @param mixed $persons
     */
    public function setPersons($persons)
    {
        $this->persons = $persons;
    }

    /**
     * @return mixed
     */
    public function getPersons()
    {
        return $this->persons;
    }



}
