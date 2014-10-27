<?php
/**
 * Price
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.07.13 18:00
 */

namespace App\ProductBundle\Entity;

use Doctrine\DBAL\Types\DecimalType;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * RE\ProductBundle\Entity\Price
 *
 * @ORM\Table(name="prices")
 * @ORM\Entity(repositoryClass="RE\ProductBundle\Entity\PriceRepository")
 */
class Price
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
     * @var Product
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="price", cascade={"persist", "remove"})
     */
    protected $product;

    /**
     * @var float
     *
     * @ORM\Column(name="list_price", type="decimal", scale=2, precision=18, nullable=true)
     */
    protected $listPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="purchase_price", type="decimal", scale=2, precision=18, nullable=true)
     */
    protected $purchasePrice;

    /**
     * @var float $sellPrice
     *
     * @ORM\Column(name="sell_price", type="decimal", scale=2, precision=18, nullable=true)
     */
    protected $sellPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="calc_on_sell_price", type="boolean", nullable=true)
     */
    protected $calcOnSellPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="special_price", type="decimal", scale=2, precision=18, nullable=true)
     */
    protected $specialPrice;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="spp_date_from", type="datetime", nullable=true)
     */
    protected $sppDateFrom;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="spp_date_to", type="datetime", nullable=true)
     */
    protected $sppDateTo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dont_show_on_front", type="boolean", nullable=true)
     */
    protected $dontShowOnFront;

    /**
     * @var float
     *
     * @ORM\Column(name="employee_commision", type="float", nullable=true)
     */
    protected $employeeCommision;

    /**
     * @var float
     * @ORM\Column(name="discaunt", type="float", nullable=true)
     */
    protected $discaunt;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\CurrencyBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

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
     * Set listPrice
     *
     * @param float $listPrice
     * @return Price
     */
    public function setListPrice($listPrice)
    {
        $this->listPrice = $listPrice;
        return $this;
    }

    /**
     * Get listPrice
     *
     * @return float
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * Set purchasePrice
     *
     * @param float $purchasePrice
     * @return Price
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set sellPrice
     *
     * @param float $sellPrice
     * @return Price
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice;
        return $this;
    }

    /**
     * Get sellPrice
     *
     * @return float
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

    /**
     * Set calcOnSellPrice
     *
     * @param boolean $calcOnSellPrice
     * @return Price
     */
    public function setCalcOnSellPrice($calcOnSellPrice)
    {
        $this->calcOnSellPrice = $calcOnSellPrice;
        return $this;
    }

    /**
     * Get calcOnSellPrice
     *
     * @return boolean
     */
    public function getCalcOnSellPrice()
    {
        return $this->calcOnSellPrice;
    }

    /**
     * Set sppDateFrom
     *
     * @param DateTime $sppDateFrom
     * @return Price
     */
    public function setSppDateFrom($sppDateFrom)
    {
        $this->sppDateFrom = $sppDateFrom;
        return $this;
    }

    /**
     * Get sppDateFrom
     *
     * @return DateTime
     */
    public function getSppDateFrom()
    {
        return $this->sppDateFrom;
    }

    /**
     * Set sppDateTo
     *
     * @param datetime $sppDateTo
     * @return Price
     */
    public function setSppDateTo($sppDateTo)
    {
        $this->sppDateTo = $sppDateTo;
        return $this;
    }

    /**
     * Get sppDateTo
     *
     * @return DateTime
     */
    public function getSppDateTo()
    {
        return $this->sppDateTo;
    }

    /**
     * Set dontShowOnFront
     *
     * @param boolean $dontShowOnFront
     * @return Price
     */
    public function setDontShowOnFront($dontShowOnFront)
    {
        $this->dontShowOnFront = $dontShowOnFront;
        return $this;
    }

    /**
     * Get dontShowOnFront
     *
     * @return boolean
     */
    public function getDontShowOnFront()
    {
        return $this->dontShowOnFront;
    }


    public function getSpecialPrice() {
        return $this->specialPrice;
    }

    public function setSpecialPrice($specialPrice) {
        $this->specialPrice = $specialPrice;

        return $this;
    }

    /**
     * Set employeeCommision
     *
     * @param float $employeeCommision
     * @return Price
     */
    public function setEmployeeCommision($employeeCommision)
    {
        $this->employeeCommision = $employeeCommision;
        return $this;
    }

    /**
     * Get employeeCommision
     *
     * @return float
     */
    public function getEmployeeCommision()
    {
        return $this->employeeCommision;
    }

    /**
     * @param Product $product
     * @return Price
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * isEmpty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->listPrice)
            && empty($this->purchasePrice)
            && empty($this->sellPrice)
            && empty($this->specialPrice)
            && empty($this->employeeCommision)
            ;
    }

    /**
     * @param float $discaunt
     */
    public function setDiscaunt($discaunt)
    {
        $this->discaunt = $discaunt;
    }

    /**
     * @return float
     */
    public function getDiscaunt()
    {
        return $this->discaunt;
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