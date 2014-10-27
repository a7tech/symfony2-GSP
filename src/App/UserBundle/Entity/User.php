<?php

namespace App\UserBundle\Entity;

use App\PersonBundle\Entity\Person;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="users")
 *
 *
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\UserBundle\Entity\PermissionsGroup")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\OneToOne(targetEntity="App\PersonBundle\Entity\Person", inversedBy="user")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     **/
    private $person;

    protected $cachedRoles = null;

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
     * Create the object
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get parent
     *
     * @return string
     */

    public function getParent()
    {
        return 'FOSUserBundle';
    }

    public function setEmail($email) {

        parent::setUsername($email);
        $this->setUsernameCanonical($email);
        return parent::setEmail($email);
    }

    public function setEmailCanonical($emailCanonical)
    {
        parent::setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }

    /**
     * @param mixed $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns the user roles
     *
     * @param bool $cached
     * @return array The roles
     */
    public function getRoles($cached = true)
    {
        if($this->cachedRoles === null || $cached === false) {
            $roles = $this->roles;

            foreach ($this->getGroups() as $group) {
                foreach ($group->getRoles() as $role) {
                    $roles[] = $role->getRole();
                }
            }

            // we need to make sure to have at least one role
            $roles[] = static::ROLE_DEFAULT;

            $this->cachedRoles =  array_unique($roles);
        }

        return $this->cachedRoles;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->cachedRoles
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->cachedRoles
            ) = $data;
    }
}