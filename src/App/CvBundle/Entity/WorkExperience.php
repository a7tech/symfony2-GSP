<?php
/**
 * Career
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 13:28
 */

namespace App\CvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Career
 *
 * @ORM\Table(name="career")
 * @ORM\Entity()
 */
class WorkExperience
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="employer", type="string", length=150)
     */
    private $employer;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="job_function", type="string", length=150)
     */
    private $jobFunction;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current", type="boolean", nullable=true)
     */
    private $current=false;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_name", type="string", length=150, nullable=true)
     */
    private $managerName;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_email", type="string", length=150, nullable=true)
     */
    private $managerEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_mobile_phone", type="string", length=12, nullable=true)
     */
    private $managerMobilePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_work_phone", type="string", length=12, nullable=true)
     */
    private $managerWorkPhone;

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
     * Get employer
     *
     * @return string
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set employer
     *
     * @param string $employer
     */
    public function setEmployer($employer)
    {
        $this->employer = $employer;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get job function
     *
     * @return string
     */
    public function getJobFunction()
    {
        return $this->jobFunction;
    }

    /**
     * Set Job function
     *
     * @param string $jobFunction
     */
    public function setJobFunction($jobFunction)
    {
        $this->jobFunction = $jobFunction;
    }

    /**
     * Get id
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
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Set End date
     *
     * @param datetime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @param string $text
     */
    public function setDescription($text)
    {
        $this->description = $text;
    }

    /**
     * Get manager's name
     *
     * @return string
     */
    public function getManagerName()
    {
        return $this->managerName;
    }

    /**
     * Set manager's name
     *
     * @param string $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    /**
     * Get manager's email
     *
     * @return string
     */
    public function getManagerEmail()
    {
        return $this->managerEmail;
    }

    /**
     * Set manager's email
     *
     * @param string $managerEmail
     */
    public function setManagerEmail($managerEmail)
    {
        $this->managerEmail = $managerEmail;
    }

    /**
     * Get manager mobile phone number
     *
     * @return string
     */
    public function getManagerMobilePhone()
    {
        return $this->managerMobilePhone;
    }

    /**
     * Set manager's mobile phone
     *
     * @param string $managerMobilePhone
     */
    public function setManagerMobilePhone($managerMobilePhone)
    {
        $this->managerMobilePhone = $managerMobilePhone;
    }

    /**
     * Get manager work phone number
     *
     * @return string
     */
    public function getManagerWorkPhone()
    {
        return $this->managerWorkPhone;
    }

    /**
     * Set manager's work phone
     * @param string $managerWorkPhone
     */
    public function setManagerWorkPhone($managerWorkPhone)
    {
        $this->managerWorkPhone = $managerWorkPhone;
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