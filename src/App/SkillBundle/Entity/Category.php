<?php
/**
 * SkillCategory
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 15.08.13 17:41
 */

namespace App\SkillBundle\Entity;

use App\CategoryBundle\Entity\Category as CommonCategory;
use App\IndustryBundle\Entity\Sector;
use App\IndustryBundle\Entity\Speciality;
use App\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Skill category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="skill_category")
 * @ORM\Entity(repositoryClass="CategoryRepository")
 */
class Category extends CommonCategory
{
    /**
     * @var Sector
     * @ORM\ManyToOne(targetEntity="App\IndustryBundle\Entity\Sector")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="sector_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $sector;

    /**
     * @var Speciality
     * @ORM\ManyToOne(targetEntity="App\IndustryBundle\Entity\Speciality")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="speciality_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $speciality;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_for_evaluation", type="boolean", options={"default"=false})
     */
    protected $useForEvaluation = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", options={"default"=0})
     * @Assert\Range(min = 0, max = 100)
     */
    protected $value = 0;

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
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param boolean $useForEvaluation
     */
    public function setUseForEvaluation($useForEvaluation)
    {
        $this->useForEvaluation = $useForEvaluation;
    }

    /**
     * @return boolean
     */
    public function getUseForEvaluation()
    {
        return $this->useForEvaluation;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}