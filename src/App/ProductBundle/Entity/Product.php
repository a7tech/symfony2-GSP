<?php

namespace App\ProductBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="App\ProductBundle\Entity\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", length=255, nullable=true)
     */
    protected $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    protected $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=127, unique=true)
     */
    protected $slug;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="products", cascade={"detach"})
     * @ORM\JoinTable(name="product_category_link",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="BrandGroup", inversedBy="products")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="brand_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $brandGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="video_link", type="string", length=255, nullable=true)
     */
    protected $videoLink;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ProductImage", mappedBy="product", cascade={"persist"})
     */
    private $images;

    /**
     * @var ProductType
     * @ORM\ManyToOne(targetEntity="ProductType")
     */
    private $productType;

    /**
     * @var Price
     *
     * @ORM\ManyToOne(targetEntity="Price", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="price_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $price;



    /**
     * @var string $productCode
     *
     * @ORM\Column(name="product_code", type="string", length=255, nullable=true)
     */
    protected $productCode;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\BarcodeBundle\Entity\Barcode", cascade={"persist"})
     * @ORM\JoinTable(name="products_barcodes",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="barcode_id", referencedColumnName="id")}
     * )
     */
    protected $barcodes;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", nullable=true)
     */
    protected $weight;

    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float", nullable=true)
     */
    protected $width;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", nullable=true)
     */
    protected $height;

    /**
     * @var float
     *
     * @ORM\Column(name="depth", type="float", nullable=true)
     */
    protected $depth;



    /**
     * @var string
     *
     * @ORM\Column(name="live_time", type="string", length=45, nullable=true)
     */
    protected $liveTime;

    /**
     * @var string
     * @ORM\Column(name="live_time_amount", type="string", length=15, nullable=true)
     */
    protected $liveTimeAmount;

    /**
     * @var string
     * @ORM\Column(name="production_time_amount", type="string", length=15, nullable=true)
     */
    protected $productionTimeAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="production_time", type="string", length=45, nullable=true)
     */
    protected $productionTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="new_from_date", type="datetime", nullable=true)
     */
    protected $newFromDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="new_to_date", type="datetime", nullable=true)
     */
    protected $newToDate;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacture_country", type="string", length=255, nullable=true)
     */
    protected $manufactureCountry;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_on_front", type="boolean", nullable=true)
     */
    protected $showOnFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="online_purchaseable", type="boolean", options={"default"=true})
     */
    protected $onlinePurchaseable = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default"=true})
     */
    protected $isActive = true;

    /**
     * @var DateTime
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * This property is used on search filter only 
     * @var DateTime
    **/
    protected $updatedAtFrom;

    /**
     * This property is used on search filter only 
     * @var DateTime
    **/
    protected $updatedAtTo;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="App\AccountProductBundle\Entity\AccountProduct", mappedBy="product")
     */
    protected $accProducts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->barcodes = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->title;
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add category
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $this->categories->add($category);
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Collection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param BrandGroup $brandGroup
     */
    public function setBrandGroup($brandGroup)
    {
        $this->brandGroup = $brandGroup;
    }

    /**
     * @return BrandGroup
     */
    public function getBrandGroup()
    {
        return $this->brandGroup;
    }

    /**
     * @param ProductType $type
     */
    public function setProductType($type)
    {
        $this->productType = $type;
    }

    /**
     * @return ProductType
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @param string $videoLink
     */
    public function setVideoLink($videoLink)
    {
        $this->videoLink = $videoLink;
    }

    /**
     * @return string
     */
    public function getVideoLink()
    {
        return $this->videoLink;
    }

    /**
     * Add image
     *
     * @param ProductImage $image
     */
    public function addImage(ProductImage $image)
    {
        $image->setProduct($this);
        $this->images->add($image);
    }

    /**
     * Remove image
     *
     * @param ProductImage $image
     */
    public function removeImage(ProductImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param Price $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set isTaxable
     *
     * @param boolean $isTaxable
     * @return Product
     */

    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
        return $this;
    }

    public function getProductCode()
    {
        return $this->productCode;
    }

    public function addBarcode($barcode)
    {
        $this->barcodes[] = $barcode;
    }

    public function removeBarcode($barcode)
    {
        $this->barcodes->removeElement($barcode);
    }

    public function getBarcodes()
    {
        return $this->barcodes;
    }

    /**
     * Set weight
     *
     * @param float $weight
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set width
     *
     * @param float $width
     * @return Product
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param float $height
     * @return Product
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set depth
     *
     * @param float $depth
     * @return Product
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get depth
     *
     * @return float
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * hasWeightAndSize
     *
     * @return bool
     */
    public function hasWeightAndSize()
    {
        return !empty($this->weight)
            || !empty($this->width)
            || !empty($this->height)
            || !empty($this->depth)
            ;
    }



    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param string $liveTime
     */
    public function setLiveTime($liveTime)
    {
        $this->liveTime = $liveTime;
    }

    /**
     * @return string
     */
    public function getLiveTime()
    {
        return $this->liveTime;
    }

    /**
     * @param string $manufactureCountry
     */
    public function setManufactureCountry($manufactureCountry)
    {
        $this->manufactureCountry = $manufactureCountry;
    }

    /**
     * @return string
     */
    public function getManufactureCountry()
    {
        return $this->manufactureCountry;
    }

    /**
     * @param \DateTime $newFromDate
     */
    public function setNewFromDate($newFromDate)
    {
        $this->newFromDate = $newFromDate;
    }

    /**
     * @return \DateTime
     */
    public function getNewFromDate()
    {
        return $this->newFromDate;
    }

    /**
     * @param \DateTime $newToDate
     */
    public function setNewToDate($newToDate)
    {
        $this->newToDate = $newToDate;
    }

    /**
     * @return \DateTime
     */
    public function getNewToDate()
    {
        return $this->newToDate;
    }

    /**
     * @param boolean $onlinePurchaseable
     */
    public function setOnlinePurchaseable($onlinePurchaseable)
    {
        $this->onlinePurchaseable = $onlinePurchaseable;
    }

    /**
     * @return boolean
     */
    public function getOnlinePurchaseable()
    {
        return $this->onlinePurchaseable;
    }

    /**
     * @param string $productionTime
     */
    public function setProductionTime($productionTime)
    {
        $this->productionTime = $productionTime;
    }

    /**
     * @return string
     */
    public function getProductionTime()
    {
        return $this->productionTime;
    }

    /**
     * @param boolean $showOnFront
     */
    public function setShowOnFront($showOnFront)
    {
        $this->showOnFront = $showOnFront;
    }

    /**
     * @return boolean
     */
    public function getShowOnFront()
    {
        return $this->showOnFront;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAtFrom()
    {
        return $this->updatedAtFrom;
    }

    /**
     * @param DateTime $updatedAtFrom
     */
    public function setUpdatedAtFrom($updatedAtFrom)
    {
        $this->updatedAtFrom = $updatedAtFrom;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAtTo()
    {
        return $this->updatedAtTo;
    }

    /**
     * @param DateTime $updatedAtTo
     */
    public function setUpdatedAtTo($updatedAtTo)
    {
        $this->updatedAtTo = $updatedAtTo;
    }

    /**
     * @param string $liveTimeAmount
     */
    public function setLiveTimeAmount($liveTimeAmount)
    {
        $this->liveTimeAmount = $liveTimeAmount;
    }

    /**
     * @return string
     */
    public function getLiveTimeAmount()
    {
        return $this->liveTimeAmount;
    }

    /**
     * @param string $productionTimeAmount
     */
    public function setProductionTimeAmount($productionTimeAmount)
    {
        $this->productionTimeAmount = $productionTimeAmount;
    }

    /**
     * @return string
     */
    public function getProductionTimeAmount()
    {
        return $this->productionTimeAmount;
    }

    /**
     * @param mixed $accProducts
     */
    public function setAccProducts($accProducts)
    {
        $this->accProducts = $accProducts;
    }

    /**
     * @return mixed
     */
    public function getAccProducts()
    {
        return $this->accProducts;
    }


}
