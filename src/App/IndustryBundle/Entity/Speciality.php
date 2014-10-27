<?php

namespace App\IndustryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Speciality
 *
 * @ORM\Table(name="industry_speciality")
 * @ORM\Entity(repositoryClass="App\IndustryBundle\Entity\SpecialityRepository")
 */
class Speciality
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
     * Title
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    protected $title='';

    /**
     * Industry Sector
     * @var Sector
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="specialities")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="sector_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * }))
     */
    protected $sector;

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
     * Set sector
     * @param \App\IndustryBundle\Entity\Sector $sector
     */
    public function setSector($sector)
    {
        $this->sector = $sector;
    }

    /**
     * Get Sector
     * @return \App\IndustryBundle\Entity\Sector
     */
    public function getSector()
    {
        return $this->sector;
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


}
