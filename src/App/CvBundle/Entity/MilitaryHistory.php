<?php
/**
 * MilitaryHistary
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 17:16
 */

namespace App\CvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * MilitaryHistory
 *
 * @ORM\Table(name="military_history")
 * @ORM\Entity()
 */
class MilitaryHistory
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
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="division", type="string", nullable=true)
     */
    private $division;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="rank", type="string", nullable=true)
     */
    private $rank;

    /**
     * @var DateTime
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
     * @var string
     *
     * @ORM\Column(name="campaign", type="string", nullable=true)
     */
    private $campaign;

    /**
     * @var string $expertise
     * @ORM\Column(name="expertise", type="string", nullable=true)
     */
    private $expertise;

    /**
     * @var string
     *
     * @ORM\Column(name="recognition", type="string", nullable=true)
     */
    private $recognition;

    /**
     * @var string
     *
     * @ORM\Column(name="disciplinary_action", type="string", nullable=true)
     */
    private $disciplinaryAction;

    /**
     * @var string
     *
     * @ORM\Column(name="discharge_status", type="string", nullable=true)
     */
    private $dischargeStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="service_status", type="string", nullable=true)
     */
    private $serviceStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

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
     * @param string $campaign
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return string
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $dischargeStatus
     */
    public function setDischargeStatus($dischargeStatus)
    {
        $this->dischargeStatus = $dischargeStatus;
    }

    /**
     * @return string
     */
    public function getDischargeStatus()
    {
        return $this->dischargeStatus;
    }

    /**
     * @param string $disciplinaryAction
     */
    public function setDisciplinaryAction($disciplinaryAction)
    {
        $this->disciplinaryAction = $disciplinaryAction;
    }

    /**
     * @return string
     */
    public function getDisciplinaryAction()
    {
        return $this->disciplinaryAction;
    }

    /**
     * @param string $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }

    /**
     * @return string
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $expertise
     */
    public function setExpertise($expertise)
    {
        $this->expertise = $expertise;
    }

    /**
     * @return string
     */
    public function getExpertise()
    {
        return $this->expertise;
    }

    /**
     * @param string $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param string $recognition
     */
    public function setRecognition($recognition)
    {
        $this->recognition = $recognition;
    }

    /**
     * @return string
     */
    public function getRecognition()
    {
        return $this->recognition;
    }

    /**
     * @param string $serviceStatus
     */
    public function setServiceStatus($serviceStatus)
    {
        $this->serviceStatus = $serviceStatus;
    }

    /**
     * @return string
     */
    public function getServiceStatus()
    {
        return $this->serviceStatus;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }
}