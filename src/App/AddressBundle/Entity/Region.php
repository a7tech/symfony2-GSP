<?php

namespace App\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Region
 *
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="App\AddressBundle\Entity\RegionRepository")
 */
class Region
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
     * Name.
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * Province.
     * @var Province
     * @ORM\ManyToOne(targetEntity="Province", cascade={"persist"}, inversedBy="regions")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="province_id", referencedColumnName="id", nullable=true)
     * }))
     */
    protected $province;

    /**
     * Province.
     * @var Country
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="regions")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     * }))
     */
    protected $country;

    public function __toString()
    {
        return $this->name;
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set province
     * @param Province $province
     */
    public function setProvince(Province $province)
    {
        $this->province = $province;
    }

    /**
     * Get province
     * @return Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry(Country $country) {
        return $this->country = $country;
    }

}
