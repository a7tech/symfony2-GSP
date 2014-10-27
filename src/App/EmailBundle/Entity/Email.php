<?php

namespace App\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="emails")
 * @ORM\Entity(repositoryClass="App\EmailBundle\Entity\EmailRepository")
 */
class Email
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
     * Email
     * @var string
     * @ORM\Column(name="email", type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="EmailType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="email_type_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $emailType;

    /**
     * @ORM\ManyToOne(targetEntity="App\PersonBundle\Entity\Person", inversedBy="emails")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * @var Person
     */
    protected $person;

    public function __toString() {
        return (string)$this->getEmail();
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
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param int $emailType
     */
    public function setEmailType($emailType)
    {
        $this->emailType = $emailType;
    }

    /**
     * @return int
     */
    public function getEmailType()
    {
        return $this->emailType;
    }

    /**
     * @param \App\EmailBundle\Entity\Person $person
     */
    public function setPerson($person)
    {

        $this->person = $person;

    }

    /**
     * @return \App\EmailBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }


}
