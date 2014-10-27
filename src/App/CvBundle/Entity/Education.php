<?php
/**
 * Education
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 12:39
 */

namespace App\CvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Education
 *
 * @ORM\Table(name="education")
 * @ORM\Entity(repositoryClass="RE\EducationBundle\Entity\EducationRepository")
 */
class Education
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=150, nullable=true)
     */
    private $institution;

    /**
     * @var string
     *
     * @ORM\Column(name="degree", type="string", length=150, nullable=true)
     */
    private $degree;

    /**
     * @var string
     *
     * @ORM\Column(name="education_level", type="string", length=150, nullable=true)
     */
    private $educationLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="gpa", type="string", length=150, nullable=true)
     */
    private $gpa;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(name="current", type="boolean", nullable=true, options={"default"=false})
     */
    private $current = false;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get educationLevel
     *
     * @return string
     */
    public function getEducationLevel() {
        return $this->educationLevel;
    }

    /**
     * Set educationLevel
     *
     * @param string $educationLevel
     */
    public function setEducationLevel($educationLevel)
    {
        $this->educationLevel=$educationLevel;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set institution
     *
     * @param string $institution
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }

    /**
     * Get degree
     *
     * @return string
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * Set degree
     *
     * @param string $degree
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;
    }

    /**
     * Get gpa
     *
     * @return string
     */
    public function getGpa()
    {
        return $this->gpa;
    }

    /**
     * Set gpa
     *
     * @param string $gpa
     */
    public function setGpa($gpa)
    {
        $this->gpa = $gpa;
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get start date
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set start date
     *
     * @param DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Get end date
     *
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set end date
     *
     * @param DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($current)
    {
        $this->current = $current;
    }
}