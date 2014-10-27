<?php

namespace App\CompanyBundle\Entity;

use App\CurrencyBundle\Entity\Currency;
use App\TaxBundle\Entity\Tax;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="App\CompanyBundle\Entity\CompanyRepository")
 */
class Company extends CommonCompany implements CompanyInfoInterface
{
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\CurrencyBundle\Entity\Currency", cascade={"persist"})
     * @ORM\JoinTable(name="companies_currencies",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="currency_id", referencedColumnName="id" , onDelete="CASCADE")})
     */
    private $currencies;

    public function __construct() {
        parent::__construct();
        $this->currencies = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $currencys
     */
    public function setCurrencies(Collection $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Has Currency
     * @return bool
     */
    public function hasCurrencies()
    {
        return !$this->currencies->isEmpty();
    }

    /**
     * Add Taxe
     * @param Taxe $Taxe
     * @return $this
     */
    public function addCurrency(Currency $currency)
    {
        if (!$this->hasCurrency($currency)) {
            $this->currencies->add($currency);
        }

        return $this;
    }

    /**
     * Remove Taxe
     * @param Taxe $Taxe
     * @return $this
     */
    public function removeCurrency(Currency $currency)
    {
        if ($this->hasCurrency($currency)) {
            $this->currencies->removeElement($currency);
        }

        return $this;
    }
}
