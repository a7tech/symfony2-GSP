<?php

namespace App\CompanyBundle\Entity;

use App\AddressBundle\Entity\Address;
use App\CurrencyBundle\Entity\Currency;
use App\EmailBundle\Entity\Email;
use App\EmploymentBundle\Entity\Employment;
use App\IndustryBundle\Entity\Speciality;
use App\LicenseBundle\Entity\License;
use App\MediaBundle\Entity\Image;
use App\PersonBundle\Entity\Person;
use App\PhoneBundle\Entity\Phone;
use App\ProductBundle\Entity\BrandGroup;
use App\ProductBundle\Entity\Category;
use App\SocialMediaBundle\Entity\SocialMedia;
use App\TaxBundle\Entity\Tax;
use App\LanguageBundle\Entity\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\SmallIntType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Company
 * @ORM\Entity
 * @ORM\Table(name="common_companies")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="company_type", type="string")
 * @ORM\DiscriminatorMap({"company" = "Company", "accountProfile" = "App\AccountBundle\Entity\AccountProfile", "commonCompany" = "CommonCompany"})
 * @ORM\Entity(repositoryClass="App\CompanyBundle\Entity\CommonCompanyRepository")
 */
class CommonCompany
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public static $companySizesArray = array('1' => '1-2', '2' => '2-5', '3' => '5-10', '4' => '10-15', '5' => '15-20', '6' => '20-30', '7' => '30-40', '8' => '50+', '9' => '100+');

    /**
     *
     * @var SmallIntType $companySize;
     *
     * @ORM\Column(name="company_size", type="smallint", nullable=true)
     */
    protected $companySize;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name = '';

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var \DateTime $createdAt
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="company_type_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     *
     * @ORM\OrderBy({"title" = "ASC"})
     */

    protected $companyType;

    /**
     * @ORM\ManyToMany(targetEntity="CompanyGroup")
     * @ORM\JoinTable(name="companies_company_groups",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="company_group_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     *
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $groups;

    /**
     * @var string $rbq
     * @ORM\Column(name="rbq", type="string", nullable=true)
     */
    protected $rbq;

    /**
     * @var \DateTime $creationDate
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\AddressBundle\Entity\Address", cascade={"all"}, inversedBy="companies")
     * @ORM\JoinTable(name="companies_addresses",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="address_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    protected $addresses;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\PhoneBundle\Entity\Phone", cascade={"all"})
     * @ORM\JoinTable(name="companies_phones",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="phone_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    protected $phones;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\SocialMediaBundle\Entity\SocialMedia", cascade={"all"})
     * @ORM\JoinTable(name="companies_social_medias",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id" ,onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="social_media_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    protected $socialMedias;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\EmploymentBundle\Entity\Employment", mappedBy="company", cascade={"all"})
     *
     */
    protected $employments;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\LicenseBundle\Entity\License", cascade={"all"})
     * @ORM\JoinTable(name="company_licenses",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="license_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    protected $licenses;

    /**
     * @ORM\ManyToOne(targetEntity="\App\IndustryBundle\Entity\Sector")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="sector_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $sector;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\IndustryBundle\Entity\Speciality", cascade={"persist"})
     * @ORM\JoinTable(name="companies_specialities",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="speciality_id", referencedColumnName="id" , onDelete="CASCADE")})
     */
    protected $specialities;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\App\LanguageBundle\Entity\Language", cascade={"persist"})
     * @ORM\JoinTable(name="companies_languages",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id" , onDelete="CASCADE")})
     */
    protected $languages;


    /**
     * @ORM\ManyToMany(targetEntity="\App\ProductBundle\Entity\BrandGroup", cascade={"persist"}, inversedBy="companies")
     * @ORM\JoinTable(name="companies_brand_groups",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="brand_group_id", referencedColumnName="id" , onDelete="CASCADE")})
     *
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $brandGroups;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\ProductBundle\Entity\Category", inversedBy="products", cascade={"detach"})
     * @ORM\JoinTable(name="company_category_link",
     *     joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $categories;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CompanyImage", mappedBy="company", cascade={"all"})
     */
    protected $images;


    /**
     * var Collection
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\SaleOrder", mappedBy="customerCompany")
     */
    protected $customerInvoice;


    public function __construct()
    {

        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->socialMedias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employments = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->brandGroups = new ArrayCollection();
        $this->licenses = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->profileFlag = false;
        $this->specialities = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->customerInvoice = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function __toString() {

        return $this->getName();
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
     * Set addresses
     * @param Collection $addresses
     */
    public function setAddresses(Collection $addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * Get addresses
     * @return ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Has addresses
     * @return bool
     */
    public function hasAddresses()
    {
        return !$this->addresses->isEmpty();
    }

    /**
     * Add address
     * @param Address $address
     * @return $this
     */
    public function addAddress(Address $address)
    {
        if (!$this->hasAddress($address)) {
            $this->addresses->add($address);
        }

        return $this;
    }

    /**
     * Remove address
     * @param Address $address
     * @return $this
     */
    public function removeAddress(Address $address)
    {
        if ($this->hasAddress($address)) {
            $this->addresses->removeElement($address);
        }

        return $this;
    }

    /**
     * Has address
     * @param Address $address
     * @return bool
     */
    public function hasAddress(Address $address)
    {
        return $this->addresses->contains($address);
    }

    /**
     * Has main Address
     * @param Collection $addresses
     * @return bool
     */
    public function hasMainAddress() {

        $addresses = $this->getAddresses();
        foreach($addresses as $address) {
            $type = $address->getIsMain();

            if ($type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Main address
     * @return Address
     */
    public function getMainAddress() {
        $addresses = $this->getAddresses();
        foreach($addresses as $address) {
            if ($address->getIsMain()) {
                return $address;
            }
        }

        return null;
    }

    /**
     * Get Main address
     * @return Address
     */
    public function getBillingAddress() {
        $addresses = $this->getAddresses();
        foreach($addresses as $address) {
            /** @var Address $address */
            if ($address->getIsBilling()) {
                return $address;
            }
        }

        return null;
    }

    /**
     * Set phones
     * @param Collection $addresses
     */
    public function setPhones(Collection $phones)
    {
        $this->phones = $phones;
    }

    /**
     * Get phones
     * @return ArrayCollection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Has phonees
     * @return bool
     */
    public function hasPhones()
    {
        return !$this->phones->isEmpty();
    }

    /**
     * Add phone
     * @param phone $phone
     * @return $this
     */
    public function addPhone(Phone $phone)
    {
        if (!$this->hasPhone($phone)) {
            $this->phones->add($phone);
        }

        return $this;
    }

    /**
     * Remove phone
     * @param phone $phone
     * @return $this
     */
    public function removePhone(Phone $phone)
    {
        if ($this->hasPhone($phone)) {
            $this->phones->removeElement($phone);
        }

        return $this;
    }

    /**
     * Has phone
     * @param phone $phone
     * @return bool
     */
    public function hasPhone(Phone $phone)
    {
        return $this->phones->contains($phone);
    }

    /**
     * Has main phone
     * @param Collection $phonees
     * @return bool
     */
    public function hasMainPhone() {

        $phones = $this->getPhones();
        foreach($phones as $phone) {
            $type = (string)$phone->getPhoneType();

            if ($type==='Main') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Main phone
     * @return ArrayCollection
     */
    public function getMainPhone() {
        $phones = $this->getPhones();
        foreach($phones as $phone) {
            $type = (string)$phone->getPhoneType();

            if ($type==='Main') {
                return $phone;
            }
        }

        return new ArrayCollection();
    }



    /**
     * Set socialMedias
     * @param Collection $addresses
     */
    public function setSocialMedias(Collection $socialMedias)
    {
        $this->socialMedias = $socialMedias;
    }

    /**
     * Get socialMedias
     * @return ArrayCollection
     */
    public function getSocialMedias()
    {
        return $this->socialMedias;
    }

    /**
     * Has socialMedias
     * @return bool
     */
    public function hasSocialMedias()
    {
        return !$this->socialMedias->isEmpty();
    }

    /**
     * Add socialMedia
     * @param SocialMedia $socialMedia
     * @return $this
     */
    public function addSocialMedia(SocialMedia $socialMedia)
    {
        if (!$this->hasSocialMedia($socialMedia)) {
            $this->socialMedias->add($socialMedia);
        }

        return $this;
    }

    /**
     * Remove socialMedia
     * @param socialMedia $socialMedia
     * @return $this
     */
    public function removeSocialMedia(SocialMedia $socialMedia)
    {
        if ($this->hasSocialMedia($socialMedia)) {
            $this->socialMedias->removeElement($socialMedia);
        }

        return $this;
    }

    /**
     * Has socialMedia
     * @param socialMedia $socialMedia
     * @return bool
     */
    public function hasSocialMedia(SocialMedia $socialMedia)
    {
        return $this->socialMedias->contains($socialMedia);
    }



    /**
     * @param DateTime $creationDate
     */
    public function setCreationDate(\DateTime $creationDate=null)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \Doctrine\DBAL\Types\SmallIntType $companySize
     */
    public function setCompanySize($companySize)
    {
        $this->companySize = $companySize;
    }

    /**
     * @return \Doctrine\DBAL\Types\SmallIntType
     */
    public function getCompanySize()
    {
        return $this->companySize;
    }


    /**
     * @param mixed $companyType
     */
    public function setCompanyType($companyType)
    {
        $this->companyType = $companyType;
    }

    /**
     * @return mixed
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Set Employers.
     * @param \Doctrine\Common\Collections\ArrayCollection $employers
     */
    public function setEmployments($employers)
    {
        foreach($employers as $employee){
            /** @var Employment $employee */
            $employee->setCompany($this);
        }

        $this->employments = $employers;
    }

    /**
     * Get Employers
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEmployments()
    {
        return $this->employments;
    }

    /**
     * Has Employers
     * @return bool
     */
    public function hasEmployments()
    {
        return !$this->employments->isEmpty();
    }

    /**
     * Add Employers
     * @param Person $employer
     * @return $this
     */
    public function addEmployment(Employment $employment)
    {
        if (!$this->hasEmployment($employment)) {
            $employment->setCompany($this);
            $this->employments->add($employment);
        }
        return $this;
    }

    /**
     * Remove Employer
     * @param Person $employer
     * @return $this
     */
    public function removeEmployment(Employment $employment)
    {
        if ($this->hasEmployment($employment)) {
            $this->employments->removeElement($employment);
        }

        return $this;
    }

    /**
     * Has Employer
     * @param Person $employer
     * @return bool
     */
    public function hasEmployment(Employment $employment)
    {
        return $this->employments->contains($employment);
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param mixed $sector
     */
    public function setSector($sector)
    {
        $this->sector = $sector;
    }

    /**
     * @return mixed
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @param string $rbq
     */
    public function setRbq($rbq)
    {
        $this->rbq = $rbq;
    }

    /**
     * @return string
     */
    public function getRbq()
    {
        return $this->rbq;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $licenses
     */
    public function setLicenses(Collection $licenses)
    {
        $this->licenses = $licenses;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLicenses()
    {
        return $this->licenses;
    }

    /**
     * Has Licenses
     * @return bool
     */
    public function hasLicenses()
    {
        return !$this->licenses->isEmpty();
    }

    /**
     * Add License
     * @param License $License
     * @return $this
     */
    public function addLicense(License $license)
    {
        if (!$this->hasLicense($license)) {
            $this->licenses->add($license);
        }

        return $this;
    }

    /**
     * Remove License
     * @param License $License
     * @return $this
     */
    public function removeLicense(License $license)
    {
        if ($this->hasLicense($license)) {
            $this->licenses->removeElement($license);
        }

        return $this;
    }

    /**
     * Has License
     * @param License $License
     * @return bool
     */
    public function hasLicense(License $license)
    {
        return $this->licenses->contains($license);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $specialities
     */
    public function setSpecialities(Collection $specialities)
    {
        $this->specialities = $specialities;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSpecialities()
    {
        return $this->specialities;
    }

    /**
     * Has Speciality
     * @return bool
     */
    public function hasSpecialities()
    {
        return !$this->specialities->isEmpty();
    }

    /**
     * Add Taxe
     * @param Taxe $Taxe
     * @return $this
     */
    public function addSpecilaity(Speciality $speciality)
    {
        if (!$this->hasSpeciality($speciality)) {
            $this->specialities->add($speciality);
        }

        return $this;
    }

    /**
     * Remove Taxe
     * @param Taxe $Taxe
     * @return $this
     */
    public function removeSpeciality(Speciality $speciality)
    {
        if ($this->hasSpeciality($speciality)) {
            $this->specialities->removeElement($speciality);
        }

        return $this;
    }

    /**
     * Has Taxe
     * @param Taxe $Taxe
     * @return bool
     */
    public function hasSpeciality(Speciality $speciality)
    {
        return $this->specialities->contains($speciality);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $languages
     */
    public function setLanguages(Collection $languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Has Language
     * @return bool
     */
    public function hasLanguages()
    {
        return !$this->languages->isEmpty();
    }

    /**
     * Add Taxe
     * @param Taxe $Taxe
     * @return $this
     */
    public function addLanguage(Language $language)
    {
        if (!$this->hasLanguage($language)) {
            $this->languages->add($language);
        }

        return $this;
    }

    /**
     * Remove Taxe
     * @param Taxe $Taxe
     * @return $this
     */
    public function removeLanguage(Language $language)
    {
        if ($this->hasLanguage($language)) {
            $this->languages->removeElement($language);
        }

        return $this;
    }

    /**
     * Has Taxe
     * @param Taxe $Taxe
     * @return bool
     */
    public function hasLanguage(Language $language)
    {
        return $this->languages->contains($language);
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
    public function setBrandGroups($brandGroup)
    {
        $this->brandGroups = $brandGroup;
    }

    /**
     * @return BrandGroup
     */
    public function getBrandGroups()
    {
        return $this->brandGroups;
    }

    public function hasBrandGroups()
    {
        return !$this->brandGroups->isEmpty();
    }

    /**
     * Add Brand Group
     * @param BrandGroup $brandGroup
     * @return $this
     */
    public function addBrandGroup(BrandGroup $brandGroup)
    {
        if (!$this->hasBrandGroup($brandGroup)) {
            $this->brandGroups->add($brandGroup);
        }

        return $this;
    }

    /**
     * Remove brandGroup
     * @param brandGroup $brandGroup
     * @return $this
     */
    public function removeBrandGroup(BrandGroup $brandGroup)
    {
        if ($this->hasBrandGroup($brandGroup)) {
            $this->phones->removeElement($brandGroup);
        }

        return $this;
    }

    /**
     * Has brandGroup
     * @param brandGroup $brandGroup
     * @return bool
     */
    public function hasBrandGroup(BrandGroup $brandGroup)
    {
        return $this->brandGroups->contains($brandGroup);
    }

    /**
     * Add image
     *
     * @param ProductImage $image
     */
    public function addImage(CompanyImage $image)
    {
        $image->setCompany($this);
        $this->images->add($image);
    }

    /**
     * Remove image
     *
     * @param ProductImage $image
     */
    public function removeImage(CompanyImage $image)
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
     * @return Image
     */
    public function getMainImage()
    {
        return $this->images->count() > 0 ? $this->images[0]->getImage() : null;
    }
}
