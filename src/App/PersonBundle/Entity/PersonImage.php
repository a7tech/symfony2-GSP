<?php

namespace App\PersonBundle\Entity;

use App\MediaBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Vlabs\MediaBundle\Annotation\Vlabs;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PersonImage
 *
 * @ORM\Table(name="person_image")
 * @ORM\Entity(repositoryClass="App\PersonBundle\Entity\PersonImageRepository")
 */
class PersonImage
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \App\PersonBundle\Entity\Image $image
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    /**
     * @return \App\PersonBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $person
     */
    public function setPersonImage(PersonImage $person)
    {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getPersonImage()
    {
        return $this->person;
    }


}
