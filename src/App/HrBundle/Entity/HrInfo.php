<?php

namespace App\HrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrInfo
 *
 * @ORM\Table(name="hr_info")
 * @ORM\Entity
 */
class HrInfo
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
     * @var boolean
     *
     * @ORM\Column(name="receive_commissions", type="boolean", options={"default"=false})
     */
    private $receiveCommissions = false;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=255, nullable=true)
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="id_number", type="string", length=255, nullable=true)
     */
    private $idNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="social_security_number", type="string", length=255, nullable=true)
     */
    private $socialSecurityNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="driving_licence", type="string", length=255, nullable=true)
     */
    private $drivingLicence;

    /**
     * @var string
     *
     * @ORM\Column(name="other_id", type="string", length=255, nullable=true)
     */
    private $otherId;

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
     * Set receiveCommissions
     *
     * @param boolean $receiveCommissions
     * @return HrInfo
     */
    public function setReceiveCommissions($receiveCommissions)
    {
        $this->receiveCommissions = $receiveCommissions;
    
        return $this;
    }

    /**
     * Get receiveCommissions
     *
     * @return boolean 
     */
    public function getReceiveCommissions()
    {
        return $this->receiveCommissions;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     * @return HrInfo
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    
        return $this;
    }

    /**
     * Get nationality
     *
     * @return string 
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set idNo
     *
     * @param string $idNumber
     * @return HrInfo
     */
    public function setIdNumber($idNumber)
    {
        $this->idNumber = $idNumber;
    
        return $this;
    }

    /**
     * Get idNo
     *
     * @return string 
     */
    public function getIdNumber()
    {
        return $this->idNumber;
    }

    /**
     * Set socialSecurityNo
     *
     * @param string $socialSecurityNumber
     * @return HrInfo
     */
    public function setSocialSecurityNumber($socialSecurityNumber)
    {
        $this->socialSecurityNumber = $socialSecurityNumber;
    
        return $this;
    }

    /**
     * Get socialSecurityNo
     *
     * @return string 
     */
    public function getSocialSecurityNumber()
    {
        return $this->socialSecurityNumber;
    }

    /**
     * Set drivingLicence
     *
     * @param string $drivingLicence
     * @return HrInfo
     */
    public function setDrivingLicence($drivingLicence)
    {
        $this->drivingLicence = $drivingLicence;
    
        return $this;
    }

    /**
     * Get drivingLicence
     *
     * @return string 
     */
    public function getDrivingLicence()
    {
        return $this->drivingLicence;
    }

    /**
     * Set otherId
     *
     * @param string $otherId
     * @return HrInfo
     */
    public function setOtherId($otherId)
    {
        $this->otherId = $otherId;
    
        return $this;
    }

    /**
     * Get otherId
     *
     * @return string 
     */
    public function getOtherId()
    {
        return $this->otherId;
    }
}
