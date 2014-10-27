<?php

namespace App\IndustryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sector
 *
 * @ORM\Table(name="industry_sector")
 * @ORM\Entity(repositoryClass="App\IndustryBundle\Entity\SectorRepository")
 */
class Sector
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
     * @var string $title
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected  $title='';

    /**
     * Industry Specialities
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Speciality", cascade={"all"}, mappedBy="sector")
     */
    protected $specialities;

    public function __construct() {
        $this->specialities = new ArrayCollection();
    }

    public function __toString() {
        return $this->title;
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
     * Set title
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set specialities
     * @param \Doctrine\Common\Collections\ArrayCollection $specialities
     */
    public function setSpecialities($specialities)
    {
        $this->specialities = $specialities;
    }

    /**
     * Get specialities
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSpecialities()
    {
        return $this->specialities;
    }

    /**
     * Has specialities
     * @return bool
     */
    public function hasSpecialities()
    {
        return !$this->specialities->isEmpty();
    }

    /**
     * Add speciality
     * @param Speciality $speciality
     * @return $this
     */
    public function addSpeciality(Speciality $speciality)
    {
        if (!$this->hasSpeciality($speciality)) {
            $this->specialities->add($speciality);
            $speciality->setSector($this);
        }

        return $this;
    }

    /**
     * Remove speciality
     * @param Speciality $speciality
     * @return $this
     */
    public function removeSpeciality(Speciality $speciality)
    {
        if ($this->hasSpeciality($speciality)) {
            $this->specialities->removeElement($speciality);
            $speciality->setSector(null);
        }

        return $this;
    }

    /**
     * Has speciality
     * @param Speciality $speciality
     * @return bool
     */
    public function hasSpeciality(Speciality $speciality)
    {
        return $this->specialities->contains($speciality);
    }


}
