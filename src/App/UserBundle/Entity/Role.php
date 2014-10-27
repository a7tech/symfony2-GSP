<?php

namespace App\UserBundle\Entity;

use Spomky\RoleHierarchyBundle\Model\Role as BaseRole;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Role
 *
 * @ORM\Table(name="roles",uniqueConstraints={@ORM\UniqueConstraint(name="role_idx", columns={"role"}) })
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\RoleRepository")
 * @UniqueEntity("role")
 */
class Role extends BaseRole implements RoleInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="role", type="string", length=255, unique=true)
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @var string Description of permission
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string Module of permission
     * @ORM\Column(name="module", type="string", nullable=true)
     */
    protected $module;

    /**
     * Constructor
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    public function getId() {
        return $this->id;
    }

    public function setParent(Role $parent) {
        $this->parent = $parent;
        return $this;
    }
    
    /**
     * Return the role field.
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Return the role field.
     * @return string 
     */
    public function __toString()
    {
        return (string) $this->role;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->role;
    }

    /**
     * Get parent
     *
     * @return \App\UserBundle\Entity\Role 
     */
    public function getParent()
    {
        return $this->parent;
    }

     /**
     * Set description
     *
     * @param string $description
     * @return Permissions
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get description for display in form
     *
     * @return string 
     */
    public function getFormdescription()
    {
        return $this->description . ' (' . $this->role . ')';
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Set module
     *
     * @param string $module
     * @return Role
     */
    public function setModule($module)
    {
        $this->module = $module;
    
        return $this;
    }

    /**
     * Get module
     *
     * @return string 
     */
    public function getModule()
    {
        return $this->module;
    }
}