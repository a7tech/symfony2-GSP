<?php

namespace App\UserBundle\Entity;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * PermissionsGroup
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\PermissionsGroupRepository")
 */
class PermissionsGroup implements GroupInterface
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\UserBundle\Entity\Role")
     * @ORM\JoinTable(name="group_roles",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

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
     * Get string
     *
     * @return string
     */

    public function __toString() {

        return (string)$this->getName();
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct($name = null)
    {
        if(!is_null($name)){
            $this->name = $name;
        }        
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param string $name
     *
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
   
    /**
     * Returns an ARRAY of Role objects with the default Role object appended.
     * @return array
     */
    public function getRoles()
    {
        return array_merge( $this->roles->toArray(), array( new Role( 'ROLE_USER' ) ) );
    }
    
    /**
     * Returns the true ArrayCollection of Roles.
     * @return Doctrine\Common\Collections\ArrayCollection 
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }
    
    /**
     * Pass a string, get the desired Role object or null.
     * @param string $role
     * @return Role|null
     */
    public function getRole( $role )
    {
        foreach ( $this->getRoles() as $roleItem )
        {
            if ( $role == $roleItem->getRole() )
            {
                return $roleItem;
            }
        }
        return null;
    }
    
    /**
     * Pass a string, checks if we have that Role. Same functionality as getRole() except returns a real boolean.
     * @param string $role
     * @return boolean
     */
    public function hasRole( $role )
    {
        if ( $this->getRole( $role ) )
        {
            return true;
        }
        return false;
    }
 
    /**
     * Adds a Role OBJECT to the ArrayCollection. Can't type hint due to interface so throws Exception.
     * @throws Exception
     * @param Role $role 
     */
    public function addRole( $role )
    {
        if ( !$role instanceof Role )
        {
            throw new \Exception( "addRole takes a Role object as the parameter" );
        }
        
        if ( !$this->hasRole( $role->getRole() ) )
        {
            $this->roles->add( $role );
        }
    }
    
    /**
     * Pass a string, remove the Role object from collection.
     * @param string $role 
     */
    public function removeRole( $role )
    {
        $roleElement = $this->getRole( $role );
        if ( $roleElement )
        {
            $this->roles->removeElement( $roleElement );
        }
    }
    
    /**
     * Pass an ARRAY of Role objects and will clear the collection and re-set it with new Roles.
     * Type hinted array due to interface.
     * @param array $roles Of Role objects.
     */
    public function setRoles( array $roles )
    {
        $this->roles->clear();
        foreach ( $roles as $role )
        {
            $this->addRole( $role );
        }
    }
    
    /**
     * Directly set the ArrayCollection of Roles. Type hinted as Collection which is the parent of (Array|Persistent)Collection.
     * @param Doctrine\Common\Collections\Collection $role 
     */
    public function setRolesCollection( Collection $collection )
    {
        $this->roles = $collection;
    }
}