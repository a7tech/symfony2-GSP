<?php
/**
 * Language
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 17:59
 */

namespace App\CvBundle\Entity;

use App\LanguageBundle\Entity\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * LangExperience
 *
 * @ORM\Table(name="lang_experience")
 * @ORM\Entity()
 */
class LangExperience
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
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="App\LanguageBundle\Entity\Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=true)
     */
    protected $language;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_read", type="boolean")
     */
    protected $read = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_write", type="boolean")
     */
    protected $write = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_speak", type="boolean")
     */
    protected $speak = false;

    /**
     * @var string
     *
     * @ORM\Column(name="proficiency", type="string", nullable=true)
     */
    protected $proficiency;

    /**
     * @var string
     *
     * @ORM\Column(name="experience", type="string", nullable=true)
     */
    protected $experience;

    /**
     * @var string
     *
     * @ORM\Column(name="lastUsed", type="string", nullable=true)
     */
    protected $lastUsed;

    /**
     * @var string
     *
     * @ORM\Column(name="interest", type="string", nullable=true)
     */
    protected $interest;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Language $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $experience
     */
    public function setExperience($experience)
    {
        $this->experience = $experience;
    }

    /**
     * @return string
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * @param string $interest
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;
    }

    /**
     * @return string
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * @param string $lastUsed
     */
    public function setLastUsed($lastUsed)
    {
        $this->lastUsed = $lastUsed;
    }

    /**
     * @return string
     */
    public function getLastUsed()
    {
        return $this->lastUsed;
    }

    /**
     * @param string $proficiency
     */
    public function setProficiency($proficiency)
    {
        $this->proficiency = $proficiency;
    }

    /**
     * @return string
     */
    public function getProficiency()
    {
        return $this->proficiency;
    }

    /**
     * @param boolean $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * @return boolean
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param boolean $speak
     */
    public function setSpeak($speak)
    {
        $this->speak = $speak;
    }

    /**
     * @return boolean
     */
    public function getSpeak()
    {
        return $this->speak;
    }

    /**
     * @param boolean $write
     */
    public function setWrite($write)
    {
        $this->write = $write;
    }

    /**
     * @return boolean
     */
    public function getWrite()
    {
        return $this->write;
    }
}