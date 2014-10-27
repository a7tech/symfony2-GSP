<?php

namespace App\TaskBundle\Entity;

use DateTime;
use App\TaxBundle\Entity\Taxation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TaskPreset
 *
 * @ORM\Table(name="task_preset")
 * @ORM\Entity(repositoryClass="App\TaskBundle\Entity\TaskPresetRepository")
 */
class TaskPreset extends TaskBase
{

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\PersonBundle\Entity\Person", cascade={"all"}, inversedBy="task")
     * @ORM\JoinTable(name="task_preset_persons",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $assignedTo;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\Taxation")
     * @ORM\JoinTable(name="task_preset_tax",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tax_id", referencedColumnName="id")}
     * )
     */
    protected $taxes;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->assignedTo = new ArrayCollection();
        $this->taxes = new ArrayCollection();
    }

    /**
     * Gets the value of assignedTo.
     *
     * @return Collection
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }
    
    /**
     * Sets the value of assignedTo.
     *
     * @param Collection $assignedTo
     * @return self
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }
}