<?php

namespace App\PhoneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Phone
 *
 * @ORM\Table(name="phones")
 * @ORM\Entity
 */
class Phone
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
     * @var PhoneIso
     * @ORM\ManyToOne(targetEntity="PhoneIso")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="phone_iso_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $phoneIsoCode;

    /**
     * @var string $number
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     */
    protected $number;

    /**
     * @var string $extension
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    protected $extension;

    /**
     * @ORM\ManyToOne(targetEntity="PhoneType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="phone_type_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $phoneType;

    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString() {

        $ext = $this->getExtension();
        $phone = '+'.$this->getPhoneIsoCode()->getPrefix().' '.$this->getNumber();
        if (!empty($ext)) {
            $phone = $phone.' extention:'.$ext;
        }
        return $phone;
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
     * Set number
     *
     * @param string $number
     * @return Phone
     */
    public function setNumber($number)
    {
        //$number = preg_replace('/[^0-9]/', '', $number);

        //need to be fixed and tested with above one :)
        $number = str_replace('-', '', $number);
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '', $number);
        $number = str_replace('_', '', $number);
        $number = str_replace(' ', '', $number);
        $this->number = trim($number);
        return $this->number;

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
     * Set extension
     *
     * @param string $extension
     * @return Phone
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set phoneType
     *
     * @param integer $phoneType
     * @return Phone
     */
    public function setPhoneType($phoneType)
    {
        $this->phoneType = $phoneType;
        return $this;
    }

    /**
     * Get phoneType
     *
     * @return integer
     */
    public function getPhoneType()
    {
        return $this->phoneType;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     * @return Phone
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     * @return Phone
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setPhoneIsoCode( $phoneIsoCode)
    {

        $this->phoneIsoCode = $phoneIsoCode;
        return $this;
    }

    /**
     * @return PhoneIso
     */
    public function getPhoneIsoCode()
    {
        return $this->phoneIsoCode;
    }


}
