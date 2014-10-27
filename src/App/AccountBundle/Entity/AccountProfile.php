<?php

namespace App\AccountBundle\Entity;

use App\CompanyBundle\Entity\CommonCompany;
use App\CompanyBundle\Entity\CompanyInfoInterface;
use App\TaxBundle\Entity\Taxation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccountProfile
 *
 * @ORM\Table(name="account_profile")
 * @ORM\Entity(repositoryClass="App\AccountBundle\Entity\AccountProfileRepository")
 */
class AccountProfile extends CommonCompany implements CompanyInfoInterface
{
    /**
     * @var Taxation
     * @ORM\OneToMany(targetEntity="App\TaxBundle\Entity\Taxation", mappedBy="company", cascade={"all"})
     **/
    protected $taxation;

    /**
     * var Collection
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\SaleOrder", mappedBy="vendorCompany")
     */
    protected $vendorInvoice;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="App\AccountBundle\Entity\AccountCurrency", mappedBy="account", cascade={"all"})
     */
    protected $currencies;

    public function __construct()
    {
        parent::__construct();
        $this->taxation = new ArrayCollection();
        $this->vendorInvoice = new ArrayCollection();
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
     * @param Collection $taxation
     */
    public function setTaxation( $taxation)
    {
        $this->taxation = $taxation;
    }

    /**
     * @return Collection
     */
    public function getTaxation()
    {
        return $this->taxation;
    }

    /**
     * Has Taxes
     * @return bool
     */
    public function hasTaxationAssigned()
    {
        return !$this->taxation->isEmpty();
    }

    /**
     * Add Taxation
     * @param Taxation $taxation
     * @return $this
     */
    public function addTaxation(Taxation $taxation)
    {
        if (!$this->hasTaxation($taxation)) {
            $taxation->setCompany($this);
            $this->taxation->add($taxation);
        }

        return $this;
    }

    /**
     * Remove Taxation
     * @param Taxation $taxation
     * @return $this
     */
    public function removeTaxation(Taxation $taxation)
    {
        if ($this->hasTaxation($taxation)) {
            $this->taxation->removeElement($taxation);
        }

        return $this;
    }

    /**
     * Has Taxation
     * @param Taxation $taxation
     * @return bool
     */
    public function hasTaxation(Taxation $taxation)
    {
        return $this->taxation->contains($taxation);
    }

    /**
     * @param array $companySizesArray
     */
    public static function setCompanySizesArray($companySizesArray)
    {
        self::$companySizesArray = $companySizesArray;
    }

    /**
     * @return array
     */
    public static function getCompanySizesArray()
    {
        return self::$companySizesArray;
    }

    /**
     * @param mixed $currencies
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @return mixed
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    public function hasCurrencies()
    {
        return !$this->currencies->isEmpty();
    }

    /**
     * Add AccountCurrency
     * @param AccountCurrency $currency
     * @return $this
     */
    public function addCurrency(AccountCurrency $currency)
    {
        if (!$this->hasCurrency($currency)) {
            $this->currencies->add($currency);
        }

        return $this;
    }

    /**
     * Remove Currency
     * @param \App\AccountBundle\Entity\AccountCurrency $currency
     * @return $this
     */
    public function removeCurrency(AccountCurrency $currency)
    {
        if ($this->hasCurrency($currency)) {
            $this->currencies->removeElement($currency);
        }

        return $this;
    }

    /**
     * Has Currency
     * @param \App\AccountBundle\Entity\AccountCurrency $currency
     * @return bool
     */
    public function hasCurrency(AccountCurrency $currency)
    {
        return $this->currencies->contains($currency);
    }

    /**
     * @return AccountCurrency
     */
    public function getDefaultCurrency()
    {
        foreach($this->currencies as $currency){
            /** @var AccountCurrency $currency */
            if($currency->getIsDefault() === true){
                return $currency;
            }
        }

        return null;
    }

    /**
     * @param mixed $customerInvoice
     */
    public function setCustomerInvoice($customerInvoice)
    {
        $this->customerInvoice = $customerInvoice;
    }

    /**
     * @return mixed
     */
    public function getCustomerInvoice()
    {
        return $this->customerInvoice;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $emails
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param boolean $profileFlag
     */
    public function setProfileFlag($profileFlag)
    {
        $this->profileFlag = $profileFlag;
    }

    /**
     * @return boolean
     */
    public function getProfileFlag()
    {
        return $this->profileFlag;
    }

    /**
     * @param mixed $vendorInvoice
     */
    public function setVendorInvoice($vendorInvoice)
    {
        $this->vendorInvoice = $vendorInvoice;
    }

    /**
     * @return mixed
     */
    public function getVendorInvoice()
    {
        return $this->vendorInvoice;
    }



}
