<?php

namespace App\CurrencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Currency
 *
 * @ORM\Table(name="currencies")
 * @ORM\Entity(repositoryClass="App\CurrencyBundle\Entity\CurrencyRepository")
 */
class Currency
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
     * @ORM\Column(name="code", type="string", length=3)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="symbol", type="string", length=10, nullable=true)
     */
    private $symbol;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="App\AccountBundle\Entity\AccountCurrency", mappedBy="currency")
     */
    private $accountCurrencies;

    public function __construct() {
        $this->accountCurrencies = new ArrayCollection();
    }

    public function __toString() {
        return $this->getName().' '.$this->getSymbol().' '.$this->getCode();
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
     * Set code
     *
     * @param string $code
     * @return Currency
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Currency
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set symbol
     *
     * @param string $symbol
     * @return Currency
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    
        return $this;
    }

    /**
     * Get symbol
     *
     * @return string 
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param mixed $accountCurrencies
     */
    public function setAccountCurrencies($accountCurrencies)
    {
        $this->accountCurrencies = $accountCurrencies;
    }

    /**
     * @return mixed
     */
    public function getAccountCurrencies()
    {
        return $this->accountCurrencies;
    }


}
