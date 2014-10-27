<?php

namespace App\AddressBundle\Entity;

use App\PhoneBundle\Entity\PhoneIso;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Country
 *
 * @ORM\Table(name="countries")
 * @ORM\Entity(repositoryClass="App\AddressBundle\Entity\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="name", type="string")
     */

    protected $name;

    /**
     * @var string
     * @ORM\Column(name="alter_name", type="string", nullable=true)
     */
    protected $alterName;

    /**
     * @var string
     * @ORM\Column(name="2_char_code", type="string", length=2)
     */

    protected $twoCharCode='';

    /**
     * @var string
     * @ORM\Column(name="3_char_code", type="string", length=3)
     */
    protected $threeCharCode='';

    /**
     * @ORM\Column(name="number_code", type="string", nullable=true)
     * @var string
     */
    protected $numberCode;

    /**
     * @ORM\Column(name="fips_country_code", type="string", nullable=true)
     * @var string
     */
    protected $fipsCountryCode;

    /**
     * @ORM\Column(name="fips_country_name", type="string", nullable=true)
     * @var string
     */
    protected $fipsCountryName;

    /**
     * @ORM\Column(name="cdh_id", type="string", nullable=true)
     * @var string
     */
    protected $cdhId;

    /**
     * @ORM\Column(name="latitude", type="string", nullable=true)
     * @var string
     */
    protected $lat;

    /**
     * @ORM\Column(name="longitude", type="string", nullable=true)
     * @var string
     */
    protected $long;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Province", cascade={"all"}, mappedBy="country")
     */
    protected $provinces;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Region", cascade={"all"}, mappedBy="country")
     */
    protected $regions;

    /**
     * @var ArrayCollection $timezones
     * @ORM\ManyToMany(targetEntity="Timezone", mappedBy="countries")
     */
    protected $timezones;

    /**
     * @var PhoneIso
     * @ORM\OneToOne(targetEntity="App\PhoneBundle\Entity\PhoneIso", mappedBy="country", cascade={"all"})
     *
     */
    protected $isoCode;

    public function __construct()
    {
        $this->provinces = new ArrayCollection();
        $this->timezones = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get Id
     * @return int
     */

    public function getId()
    {
        return $this->id;
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
     * Set name
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Get provinces
     * @return ArrayCollection
     */

    public function getProvinces()
    {
        return $this->provinces;
    }

    /**
     * Set provinces
     * @param Collection $provinces
     * @return $this
     */

    public function setProvinces(Collection $provinces)
    {
        $this->provinces = $provinces;

        return $this;
    }

    /**
     * Has provinces
     * @return bool
     */
    public function hasProvinces()
    {
        return !$this->provinces->isEmpty();
    }

    /**
     * Add province
     * @param Province $province
     * @return $this
     */
    public function addProvince(Province $province)
    {
        if (!$this->hasProvince($province)) {
            $this->provinces->add($province);
            $province->setCountry($this);
        }

        return $this;
    }

    /**
     * Remove province
     * @param Province $province
     * @return $this
     */
    public function removeProvince(Province $province)
    {
        if ($this->hasProvince($province)) {
            $this->provinces->removeElement($province);
            $province->setCountry(null);
        }

        return $this;
    }

    /**
     * Has province
     * @param Province $province
     * @return bool
     */
    public function hasProvince(Province $province)
    {
        return $this->provinces->contains($province);
    }

    /**
     * Set Timezones
     * @param ArrayCollection $timezones
     */
    public function setTimezones(Collection $timezones)
    {
        $this->timezones = $timezones;
    }

    /**
     * Set Timezones
     * @return ArrayCollection
     */
    public function getTimezones()
    {
        return $this->timezones;
    }

    /**
     * Set AlterName
     * @param string $alterName
     */
    public function setAlterName($alterName)
    {
        $this->alterName = $alterName;
    }

    /**
     * Get AlterName
     * @return string
     */
    public function getAlterName()
    {
        return $this->alterName;
    }

    /**
     * Set CDHID
     * @param string $cdhId
     */
    public function setCdhId($cdhId)
    {
        $this->cdhId = $cdhId;
    }

    /**
     * Get CDHID
     * @return string
     */
    public function getCdhId()
    {
        return $this->cdhId;
    }

    /**
     * Set FISP Country Code
     * @param string $fipsCountryCode
     */
    public function setFipsCountryCode($fipsCountryCode)
    {
        $this->fipsCountryCode = $fipsCountryCode;
    }

    /**
     * Get FISP Country Code
     * @return string
     */
    public function getFipsCountryCode()
    {
        return $this->fipsCountryCode;
    }

    /**
     * Set FISP Country Name
     * @param string $fipsCountryName
     */
    public function setFipsCountryName($fipsCountryName)
    {
        $this->fipsCountryName = $fipsCountryName;
    }

    /**
     * Get FISP Country Name
     * @return string
     */
    public function getFipsCountryName()
    {
        return $this->fipsCountryName;
    }

    /**
     * Set Latitude
     * @param string $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * Get Latitude
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set Longitude
     * @param string $long
     */
    public function setLong($long)
    {
        $this->long = $long;
    }

    /**
     * Get Longitude
     * @return string
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * Set Number Code
     * @param string $numberCode
     */
    public function setNumberCode($numberCode)
    {
        $this->numberCode = $numberCode;
    }

    /**
     * Get Number Code
     * @return string
     */
    public function getNumberCode()
    {
        return $this->numberCode;
    }

    /**
     * Set three Char Code
     * @param string $threeCharCode
     */
    public function setThreeCharCode($threeCharCode)
    {
        $this->threeCharCode = $threeCharCode;
    }

    /**
     * Get three Char Code
     * @return string
     */
    public function getThreeCharCode()
    {
        return $this->threeCharCode;
    }

    /**
     * Set two Char Code
     * @param string $twoCharCode
     */
    public function setTwoCharCode($twoCharCode)
    {
        $this->twoCharCode = $twoCharCode;
    }

    /**
     * Get two Char Code
     * @return string
     */
    public function getTwoCharCode()
    {
        return $this->twoCharCode;
    }

    /**
     * @param \App\PhoneBundle\Entity\PhoneIso $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * @return \App\PhoneBundle\Entity\PhoneIso
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }


}
