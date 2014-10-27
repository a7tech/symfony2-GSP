<?php

namespace App\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountCurrency
 *
 * @ORM\Table(name="account_currencies")
 * @ORM\Entity(repositoryClass="App\AccountBundle\Entity\AccountCurrencyRepository")
 */
class AccountCurrency
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
     * @var boolean
     *
     * @ORM\Column(name="isDefault", type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float", nullable=true)
     */
    private $rate;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\CurrencyBundle\Entity\Currency", inversedBy="accountCurrencies")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\AccountBundle\Entity\AccountProfile", inversedBy="currencies")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

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
     * Set isDefault
     *
     * @param boolean $isDefault
     * @return AccountCurrency
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    
        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean 
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set rate
     *
     * @param float $rate
     * @return AccountCurrency
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    
        return $this;
    }

    /**
     * Get rate
     *
     * @return float 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }


}
