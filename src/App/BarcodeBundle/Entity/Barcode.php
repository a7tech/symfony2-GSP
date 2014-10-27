<?php

namespace App\BarcodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Barcode
 *
 * @ORM\Table(name="barcode")
 * @ORM\Entity
 */
class Barcode
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
     * @var BarcodeType
     *
     * @ORM\ManyToOne(targetEntity="BarcodeType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="barcode_type_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $barcodeType;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

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
     * Set type
     *
     * @param BarcodeType $type
     * @return Barcode
     */
    public function setBarcodeType($type)
    {
        $this->barcodeType = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return BarcodeType
     */
    public function getBarcodeType()
    {
        return $this->barcodeType;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Barcode
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->barcodeType.' '.$this->number;
    }
}
