<?php

namespace App\TaxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taxation
 *
 * @ORM\Table(name="taxation")
 * @ORM\Entity(repositoryClass="App\TaxBundle\Entity\TaxationRepository")
 */
class Taxation implements TaxationInfoInterface
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
     * @var integer
     *
     * @ORM\Column(name="number", type="string", length=50)
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\AccountBundle\Entity\AccountProfile", inversedBy="taxations")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     **/
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="TaxType", inversedBy="taxations")
     * @ORM\JoinColumn(name="tax_type_id", referencedColumnName="id")
     **/
    private $taxType;


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
     * Set number
     *
     * @param string $number
     * @return Taxation
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $taxType
     */
    public function setTaxType($taxType)
    {
        $this->taxType = $taxType;
    }

    /**
     * @return TaxType
     */
    public function getTaxType()
    {
        return $this->taxType;
    }

    public function getTaxTypeString()
    {
        return (string)$this->taxType;
    }

    public function getRate($as_integer = false)
    {
        return $this->getTaxType()->getRate($as_integer);
    }

    public function getName()
    {
        return $this->getTaxType()->getName();
    }

    public function __toString()
    {
        return $this->getName().' - '.($this->getRate(true)).'% ('.$this->number.')';
    }

}
