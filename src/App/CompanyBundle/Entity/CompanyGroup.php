<?php

namespace App\CompanyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyGroup
 *
 * @ORM\Table(name="company_groups")
 * @ORM\Entity(repositoryClass="App\CompanyBundle\Entity\CompanyGroupRepository")
 */
class CompanyGroup
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
     * @var string $title
     *
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;


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
     * @return CompanyGroup
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
