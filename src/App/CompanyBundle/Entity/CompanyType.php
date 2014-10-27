<?php

namespace App\CompanyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyType
 *
 * @ORM\Table(name="company_types")
 * @ORM\Entity(repositoryClass="App\CompanyBundle\Entity\CompanyTypeRepository")
 */
class CompanyType
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
     * @ORM\Column(name="name", type="string")
     *
     */
    protected $name='';

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
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


}
