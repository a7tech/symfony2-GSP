<?php

namespace App\SocialMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SocialMedia
 *
 * @ORM\Table(name="social_media")
 * @ORM\Entity
 */
class SocialMedia
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
     * @ORM\ManyToOne(targetEntity="SocialMediaType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="sm_type_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $socialMediaType;

    /**
     * @var string $content
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    protected $content;

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
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $socialMediaType
     */
    public function setSocialMediaType($socialMediaType)
    {
        $this->socialMediaType = $socialMediaType;
    }

    /**
     * @return mixed
     */
    public function getSocialMediaType()
    {
        return $this->socialMediaType;
    }


}
