<?php
/**
 * Skill
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 20.08.13 16:20
 */

namespace App\SkillBundle\Entity;

use App\IndustryBundle\Entity\Sector;
use App\IndustryBundle\Entity\Speciality;
use App\PersonBundle\Entity\Person;
use App\SkillBundle\Entity\Category;
use Doctrine\ORM\Mapping as ORM;

/**
 * Skill
 *
 * @ORM\Table(name="skills")
 * @ORM\Entity
 */
class Skill
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
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="App\PersonBundle\Entity\Person", inversedBy="skills")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    /**
     * @var Sector
     *
     * @ORM\ManyToOne(targetEntity="App\IndustryBundle\Entity\Sector")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="sector_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $sector;

    /**
     * @var Speciality
     *
     * @ORM\ManyToOne(targetEntity="App\IndustryBundle\Entity\Speciality")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="speciality_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $speciality;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="skill_category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $category;

    /**
     * @var string
     *
     * @ORM\Column(name="proficiency", type="string", nullable=true)
     */
    protected $proficiency;

    /**
     * @var string
     *
     * @ORM\Column(name="experience", type="string", nullable=true)
     */
    protected $experience;

    /**
     * @var string
     *
     * @ORM\Column(name="lastUsed", type="string", nullable=true)
     */
    protected $lastUsed;

    /**
     * @var string
     *
     * @ORM\Column(name="interest", type="string", nullable=true)
     */
    protected $interest;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Sector $sector
     */
    public function setSector($sector)
    {
        $this->sector = $sector;
    }

    /**
     * @return Sector
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @param Speciality $speciality
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;
    }

    /**
     * @return Speciality
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $proficiency
     */
    public function setProficiency($proficiency)
    {
        $this->proficiency = $proficiency;
    }

    /**
     * @return string
     */
    public function getProficiency()
    {
        return $this->proficiency;
    }

    /**
     * @param string $experience
     */
    public function setExperience($experience)
    {
        $this->experience = $experience;
    }

    /**
     * @return string
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * @param string $lastUsed
     */
    public function setLastUsed($lastUsed)
    {
        $this->lastUsed = $lastUsed;
    }

    /**
     * @return string
     */
    public function getLastUsed()
    {
        return $this->lastUsed;
    }

    /**
     * @param mixed $interest
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;
    }

    /**
     * @return mixed
     */
    public function getInterest()
    {
        return $this->interest;
    }
}