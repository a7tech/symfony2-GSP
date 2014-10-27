<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.02.14
 * Time: 14:40
 */

namespace App\CompanyBundle\Entity;

use App\AccountBundle\Entity\AccountCurrency;
use App\AccountBundle\Entity\AccountProfile;
use App\AccountBundle\Entity\AccountProfileInfoInterface;
use App\AddressBundle\Entity\AddressCopy;
use App\CurrencyBundle\Entity\Currency;
use App\TaxBundle\Entity\TaxationCopy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\MediaBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;
use Vlabs\MediaBundle\Annotation\Vlabs;

/**
 * Company
 *
 * @ORM\Table(name="company_copy")
 * @ORM\Entity()
 */
class CompanyCopy implements AccountProfileInfoInterface
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string $rbq
     * @ORM\Column(name="rbq", type="string", nullable=true)
     */
    protected $rbq;

    /**
     * @var AddressCopy
     * 
     * @ORM\ManyToOne(targetEntity="App\AddressBundle\Entity\AddressCopy", cascade="all")
     * @ORM\JoinColumn(name="address_copy_id", referencedColumnName="id", nullable=true)
     */
    protected $billingAddress;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", cascade={"persist"}, orphanRemoval=true))
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     *
     * @Vlabs\Media(identifier="media_image", upload_dir="invoices/company-logo/")
     * @Assert\Valid()
     */
    protected $mainImage;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\TaxBundle\Entity\TaxationCopy", mappedBy="companyCopy", cascade={"persist"})
     */
    protected $taxation;

    public function __construct(CommonCompany $company)
    {
        $this->name = $company->getName();
        $this->rbq = $company->getRbq();

        $companyBillingAddress = $company->getBillingAddress();
        if($companyBillingAddress !== null){
            $this->billingAddress = new AddressCopy($companyBillingAddress);
        }

        //copy main image
        $main_image = $company->getMainImage();
        if($main_image !== null && file_exists($main_image->getPath())){
            $path = 'invoices/company-logo/';
            $new_file = $path.time().'-'.$main_image->getName();
            copy($main_image->getPath(), $new_file);

            $main_image_copy = new Image();
            $main_image_copy->setPath($new_file);
            $main_image_copy->setName($main_image->getName());
            $main_image_copy->setSize($main_image->getSize());
            $main_image_copy->setContentType($main_image->getContentType());

            $this->mainImage = $main_image_copy;
        }

        //taxation copy
        if($company instanceof AccountProfile){
            foreach($company->getTaxation() as $tax){
                $this->taxation[] = new TaxationCopy($tax, $this);
            }
        }

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \App\AddressBundle\Entity\AddressCopy
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @return \App\MediaBundle\Entity\Image
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTaxation()
    {
        return $this->taxation;
    }

    /**
     * @return string
     */
    public function getRbq()
    {
        return $this->rbq;
    }
}