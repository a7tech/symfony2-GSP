<?php

namespace App\AddressBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Province
 *
 * @ORM\Table(name="province")
 * @ORM\Entity(repositoryClass="App\AddressBundle\Entity\ProvinceRepository")
 */
class Province
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
     * Alternative name.
     * @ORM\Column(name="alter_name", type="string", nullable=true)
     * @var string
     */
    protected $alterName;

    /**
     * Name.
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * Level name.
     * @ORM\Column(name="level_name", type="string", nullable=true)
     * @var string
     */
    protected $levelName;


    /**
     * Iso code.
     * @ORM\Column(name="iso_code", type="string")
     * @var string
     */
    protected $isoCode;

    /**
     * CHI ID.
     * @ORM\Column(name="cdh_id", type="string")
     * @var string
     */

    protected $cdhId;

    /**
     * Country.
     * @var Country
     * @ORM\ManyToOne(targetEntity="Country", cascade={"persist"}, inversedBy="provinces")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     * }))
     */
    protected $country;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Region", cascade={"all"}, mappedBy="province")
     */
    protected $regions;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

    /**
     * Get id
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
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get country
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     * @param Country $country
     */
    public function setCountry(Country $country = null)
    {
        $this->country = $country;
    }

    /**
     * Get regions
     * @return ArrayCollection
     */

    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Set regions
     * @param Collection $regions
     * @return $this
     */

    public function setRegions(Collection $regions)
    {
        $this->regions = $regions;

        return $this;
    }

    /**
     * Has regions
     * @return bool
     */
    public function hasRegions()
    {
        return !$this->regions->isEmpty();
    }

    /**
     * Add regions
     * @param Region $region
     * @return $this
     */
    public function addRegion(Region $region)
    {
        if (!$this->hasRegion($region)) {
            $this->regions->add($region);
            $region->setProvince($this);
        }

        return $this;
    }

    /**
     * Remove region
     * @param Region $region
     * @return $this
     */
    public function removeProvince(Region $region)
    {
        if ($this->hasRegion($region)) {
            $this->regions->removeElement($region);
            $region->setProvince(null);
        }

        return $this;
    }

    /**
     * Has region
     * @param Region $region
     * @return bool
     */
    public function hasRegion(Region $region)
    {
        return $this->regions->contains($region);
    }

    /**
     * Set Alter name
     * @param string $alterName
     */
    public function setAlterName($alterName)
    {
        $this->alterName = $alterName;
    }

    /**
     * Get Alter name
     * @return string
     */
    public function getAlterName()
    {
        return $this->alterName;
    }

    /**
     * Set CDH ID
     * @param mixed $cdhId
     */
    public function setCdhId($cdhId)
    {
        $this->cdhId = $cdhId;
    }

    /**
     * Get CDH ID
     * @return mixed
     */
    public function getCdhId()
    {
        return $this->cdhId;
    }

    /**
     * Set Iso Code
     * @param string $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * Get Iso Code
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set Level name
     * @param string $levelName
     */
    public function setLevelName($levelName)
    {
        $this->levelName = $levelName;
    }

    /**
     * Get Level name
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }


}
