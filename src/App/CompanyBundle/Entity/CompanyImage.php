<?php

namespace App\CompanyBundle\Entity;

use App\MediaBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vlabs\MediaBundle\Annotation\Vlabs;

/**
 * CompanyImage
 *
 * @ORM\Table(name="company_image")
 * @ORM\Entity(repositoryClass="App\CompanyBundle\Entity\CompanyImageRepository")
 */
class CompanyImage
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
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", cascade={"persist", "remove"}, orphanRemoval=true))
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     *
     * @Vlabs\Media(identifier="media_image", upload_dir="upload/product_images")
     * @Assert\Valid()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="CommonCompany", inversedBy="images")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     **/
    private $company;

    /**
     * @var string
     * @ORM\Column(name="image_name", type="string", length=50)
     */
    private $imageName;

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
     * @param Image $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $company
     */
    public function setCompany(CommonCompany $company)
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
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }



}
