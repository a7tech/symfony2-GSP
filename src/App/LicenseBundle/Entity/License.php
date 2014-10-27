<?php

namespace App\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * License
 *
 * @ORM\Table(name="license")
 * @ORM\Entity
 */
class License
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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var LicenseType
     * @ORM\ManyToOne(targetEntity="LicenseType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="license_type_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $licenseType;


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
     * Set code
     *
     * @param string $code
     * @return License
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set licenseType
     *
     * @param \stdClass $licenseType
     * @return License
     */
    public function setLicenseType(LicenseType $licenseType)
    {
        $this->licenseType = $licenseType;

        return $this;
    }

    /**
     * Get licenseType
     *
     * @return \stdClass 
     */
    public function getLicenseType()
    {
        return $this->licenseType;
    }
}
