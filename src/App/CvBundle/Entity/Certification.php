<?php
/**
 * Certification
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 13:20
 */

namespace App\CvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Certification
 *
 * @ORM\Table(name="certification")
 * @ORM\Entity()
 */
class Certification
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
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="first_date", type="datetime")
     */
    private $firstDate;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", nullable=true)
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="from_date", type="datetime")
     */
    private $fromDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="to_date", type="datetime", nullable=true)
     */
    private $toDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current", type="boolean", options={"defualt"=false})
     */
    private $current = false;

    public function __construct()
    {
        $this->firstDate = new DateTime();
        $this->fromDate = new DateTime();
        $this->toDate = new DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getFirstDate()
    {
        return $this->firstDate;
    }

    public function setFirstDate(DateTime $firstDate)
    {
        $this->firstDate = $firstDate;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getFromDate()
    {
        return $this->fromDate;
    }

    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    public function getToDate()
    {
        return $this->toDate;
    }

    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
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