<?php
namespace App\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task categories
 *
 * @ORM\Table(name="task_categories")
 * @ORM\Entity
 */
class TaskCategory
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
     * @var integer
     *
     * @ORM\Column(name="project_id", type="integer", nullable=false)
     */
    private $projectId;
    
    /**
     * @ORM\Column(name="name", type="string")
     *
     */
    protected $name='';

    /**
     * @var integer
     *
     * @ORM\Column(name="assigned_to", type="integer", nullable=true)
     */
    private $assignedTo;

    /**
     * Get projectId
     *
     * @return integer 
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
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

    public function __toString() {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get assignedTo
     *
     * @return integer 
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * @param $assignedTo
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
    }
}