<?php
/**
 * Reference
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 17:27
 */

namespace App\CvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reference
 *
 * @ORM\Table(name="reference")
 * @ORM\Entity()
 */
class Reference
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
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string $email
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=12, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", nullable=true)
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="relationship", type="string", nullable=true)
     */
    private $relationship;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="lengthTime", type="integer", nullable=true)
     */
    private $lengthTime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="canContact", type="boolean", nullable=true, options={"default"=false})
     */
    private $canContact = false;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    public function getRelationship()
    {
        return $this->relationship;
    }

    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getLengthTime()
    {
        return $this->lengthTime;
    }

    public function setLengthTime($lengthTime)
    {
        $this->lengthTime = $lengthTime;
    }

    public function getCanContact()
    {
        return $this->canContact;
    }

    public function setCanContact($canContact)
    {
        $this->canContact = $canContact;
    }
}