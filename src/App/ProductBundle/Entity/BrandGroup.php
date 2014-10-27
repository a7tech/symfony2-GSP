<?php
/**
 * BrandGroup
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 01.08.13 13:09
 */

namespace App\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\ProductBundle\Entity\BrandGroup
 *
 * @ORM\Table(name="brand_groups")
 * @ORM\Entity(repositoryClass="App\ProductBundle\Entity\BrandGroupRepository")
 */
class BrandGroup
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    protected $description = '';

    /**
     * @var float
     *
     * @ORM\Column(name="purchase_coeff", type="float", nullable=true)
     */
    protected $purchaseCoeff;

    /**
     * @var float
     *
     * @ORM\Column(name="resell_coeff", type="float", nullable=true)
     */
    protected $resellCoeff;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="brandGroup", cascade={"persist", "remove"})
     */
    protected $products;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CompanyBundle\Entity\CommonCompany", mappedBy="brandGroups")
     */
    protected $companies;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getTitle();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return BrandGroup
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
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
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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

    public function setPurchaseCoeff($purchaseCoeff)
    {
        $this->purchaseCoeff = $purchaseCoeff;
        return $this;
    }

    public function getPurchaseCoeff()
    {
        return $this->purchaseCoeff;
    }

    public function setResellCoeff($resellCoeff)
    {
        $this->resellCoeff = $resellCoeff;
        return $this;
    }

    public function getResellCoeff()
    {
        return $this->resellCoeff;
    }

    /**
     * getProducts
     *
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * getCompanies
     *
     * @return Collection
     */
    public function getCompanies()
    {
        return $this->companies;
    }
}