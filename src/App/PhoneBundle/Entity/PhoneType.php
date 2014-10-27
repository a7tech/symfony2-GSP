<?php

namespace App\PhoneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PhoneType
 *
 * @ORM\Table(name="phone_type")
 * @ORM\Entity(repositoryClass="App\PhoneBundle\Entity\PhoneTypeRepository")
 */
class PhoneType
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
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     * Set name.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


}
