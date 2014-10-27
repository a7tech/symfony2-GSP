<?php

namespace App\HrBundle\Entity;

use App\CurrencyBundle\Entity\Currency;
use Doctrine\ORM\Mapping as ORM;

/**
 * SalaryInfo
 *
 * @ORM\Table(name="salary_info")
 * @ORM\Entity
 */
class SalaryInfo
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
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="App\CurrencyBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=true)
     */
    private $currency;

    /**
     * @var SalaryType
     *
     * @ORM\ManyToOne(targetEntity="SalaryType")
     * @ORM\JoinColumn(name="salary_type_id", referencedColumnName="id", nullable=true)
     */
    private $salaryType;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="monday", type="integer", nullable=true)
     */
    private $monday;

    /**
     * @var integer
     *
     * @ORM\Column(name="tuesday", type="integer", nullable=true)
     */
    private $tuesday;

    /**
     * @var integer
     *
     * @ORM\Column(name="wednesday", type="integer", nullable=true)
     */
    private $wednesday;

    /**
     * @var integer
     *
     * @ORM\Column(name="thursday", type="integer", nullable=true)
     */
    private $thursday;

    /**
     * @var integer
     *
     * @ORM\Column(name="friday", type="integer", nullable=true)
     */
    private $friday;

    /**
     * @var integer
     *
     * @ORM\Column(name="saturday", type="integer", nullable=true)
     */
    private $saturday;

    /**
     * @var integer
     *
     * @ORM\Column(name="sunday", type="integer", nullable=true)
     */
    private $sunday;

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
     * Set currency
     *
     * @param Currency $currency
     * @return SalaryInfo
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set salary
     *
     * @param SalaryType $salaryType
     * @return SalaryInfo
     */
    public function setSalaryType($salaryType)
    {
        $this->salaryType = $salaryType;
    
        return $this;
    }

    /**
     * Get salary
     *
     * @return SalaryType
     */
    public function getSalaryType()
    {
        return $this->salaryType;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return SalaryInfo
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }


    /**
     * Set :monday
     *
     * @param integer $monday
     * @return SalaryInfo
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;
    
        return $this;
    }

    /**
     * Get :monday
     *
     * @return integer 
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param integer $tuesday
     * @return SalaryInfo
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    
        return $this;
    }

    /**
     * Get tuesday
     *
     * @return integer 
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param integer $wednesday
     * @return SalaryInfo
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    
        return $this;
    }

    /**
     * Get wednesday
     *
     * @return integer 
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param integer $thursday
     * @return SalaryInfo
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;
    
        return $this;
    }

    /**
     * Get thursday
     *
     * @return integer 
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param integer $friday
     * @return SalaryInfo
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;
    
        return $this;
    }

    /**
     * Get friday
     *
     * @return integer 
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set saturday
     *
     * @param integer $saturday
     * @return SalaryInfo
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;
    
        return $this;
    }

    /**
     * Get saturday
     *
     * @return integer 
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * Set sunday
     *
     * @param integer $sunday
     * @return SalaryInfo
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;
    
        return $this;
    }

    /**
     * Get sunday
     *
     * @return integer 
     */
    public function getSunday()
    {
        return $this->sunday;
    }
}
