<?php

namespace App\BarcodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BarcodeType
 *
 * @ORM\Table(name="barcode_type")
 * @ORM\Entity(repositoryClass="App\BarcodeBundle\Entity\BarcodeTypeRepository")
 */
class BarcodeType
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    public function __toString()
    {
        return (string)$this->name;
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
     * Set title
     *
     * @param string $title
     * @return BarcodeType
     */
    public function setName($title)
    {
        $this->name = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
