<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.02.14
 * Time: 19:02
 */

namespace App\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address_copy")
 * @ORM\Entity
 */
class AddressCopy
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
     * @var string
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", nullable=true)
     */
    protected $province;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", nullable=true)
     */
    protected $region;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", nullable=true)
     */
    protected $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", nullable=true)
     */
    protected $street;

    /**
     * @ORM\Column(name="building", type="string", nullable=true)
     * @var string
     */
    protected $building;

    /**
     * Suite
     * @ORM\Column(name="suite", type="string", nullable=true)
     * @var string
     */
    protected $suite;

    /**
     * @ORM\Column(name="contact", type="string", nullable=true)
     * @var string
     */
    protected $contact;

    public function __construct(Address $address)
    {
        $this->country = (string)$address->getCountry();
        $this->province = (string)$address->getProvince();
        $this->region = (string)$address->getRegion();
        $this->city = $address->getCity();
        $this->postcode = $address->getPostcode();
        $this->street = $address->getStreet();
        $this->building = $address->getBuilding();
        $this->suite = $address->getSuite();
    }

    public function __toString()
    {
        return $this->getFormatted(null, ' ');
    }

    public function getFormatted($suite_label = null, $lineSeparator = '<br>')
    {
        $address = '';

        if ($this->building !== null) {
            $address .= $this->building;
        }

        if ($this->street !== null) {
            if(strlen($address) > 0){
                $address .= ', ';
            }
            $address .= $this->street;
        }

        if ($this->suite) {
            if(strlen($address) > 0){
                $address .= $lineSeparator;
            }

            $address .= '('.($suite_label !== null ? $suite_label.' ' : '').$this->suite.')';
        }

        if(strlen($address) > 0){
            $address .= $lineSeparator;
        }

        if ($this->city !== null) {
            $address .= $this->city;
        }
        if ($this->postcode !== null) {
            if(strlen($address) > 0){
                $address .= ', ';
            }

            $address .= $this->postcode;
        }
        if ($this->province) {
            if(strlen($address) > 0){
                $address .= ', ';
            }
            $address .= $this->province;
        }
        if ($this->country) {
            if(strlen($address) > 0){
                $address .= ', ';
            }
            $address .= $this->country;
        }



        return $address;
    }

    /**
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getSuite()
    {
        return $this->suite;
    }


} 