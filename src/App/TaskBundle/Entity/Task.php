<?php

namespace App\TaskBundle\Entity;

use App\InvoiceBundle\Entity\InvoiceTask;
use App\PlaceBundle\Entity\Place;
use App\ProjectBundle\Entity\ContractCategory;
use App\ProjectBundle\Entity\Project;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="App\TaskBundle\Entity\TaskRepository")
 * @Assert\Callback(methods={"isTypeValid"})
 * @Assert\Callback(methods={"isParentValid"})
 */
class Task extends TaskBase
{
    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="App\TaskBundle\Entity\Task", inversedBy="parentOf")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id", nullable=true)
     */
    protected $pid;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Task", mappedBy="pid")
     */
    protected $parentOf;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="App\ProjectBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    protected $project;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\PersonBundle\Entity\Person", inversedBy="task")
     * @ORM\JoinTable(name="task_persons",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $assignedTo;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\InvoiceTask", mappedBy="task")
     */ 
    protected $invoicesTasks;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="TaskFile", mappedBy="task", cascade={"persist"})
     */
    private $files;

    /**
     * @var bool
     * @ORM\Column(name="is_contracted", type="boolean")
     */
    protected $isContracted = false;

    protected $typeChanged = false;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\Taxation")
     * @ORM\JoinTable(name="task_tax",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tax_id", referencedColumnName="id")}
     * )
     */
    protected $taxes;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\TaxBundle\Entity\TaxationCopy", cascade={"persist"})
     * @ORM\JoinTable(name="task_tax_copy",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="taxation_copy_id", referencedColumnName="id")}
     * )
     */
    protected $taxesCopies;

    /**
     * @var bool
     * @ORM\Column(name="taxes_locked", type="boolean")
     */
    protected $taxesLocked = false;

    /**
     * Estimated end. Holds false if never computed
     *
     * @var bool
     */
    protected $estimatedEnd = false;

    /**
     * Estimated end. Holds false if never computed
     *
     * @var bool
     */
    protected $estimatedStart = false;

    /**
     * @var ContractCategory
     * 
     * @ORM\ManyToOne(targetEntity="App\ProjectBundle\Entity\ContractCategory")
     * @ORM\JoinColumn(name="contract_category_id", referencedColumnName="id")
     */
    protected $contractCategory;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->parentOf = new ArrayCollection();
        $this->assignedTo = new ArrayCollection();
        $this->taxes = new ArrayCollection();
        $this->invoicesTasks = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    /**
     * Gets the value of status.
     *
     * @return \App\TaskBundle\Entity\Task
     */
    public function getPid()
    {
        return $this->pid;
    }
    
    /**
     * Sets the value of pid
     *
     * @param \App\TaskBundle\Entity\Task $pid
     * @return self
     */
    public function setPid(Task $pid = null)
    {
        $this->pid = $pid;

        if($pid === null){
            $this->setDependency(null);
        }


        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getParentOf()
    {
        return $this->parentOf;
    }

    /**
     * @param ArrayCollection $parentOf
     */
    public function setParentOf($parentOf)
    {
        $this->parentOf = $parentOf;
    }

    /**
     * Gets the value of project.
     *
     * @return \App\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }
    
    /**
     * Set Project
     *
     * @param \App\ProjectBundle\Entity\Project $project
     * @return Project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
        return $this;
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

    public function setType($type)
    {
        $this->typeChanged = ($type != $this->type);
        return parent::setType($type);
    }

    /**
     * @param boolean $isContracted
     */
    public function setIsContracted($isContracted)
    {
        $this->isContracted = $isContracted;
    }

    /**
     * @return boolean
     */
    public function isContracted()
    {
        return $this->isContracted;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }




    public function isTypeValid(ExecutionContextInterface $context)
    {
        if($this->typeChanged){
            switch($this->type){
                case self::TYPE_PAYABLE:
                    if($this->project->getType() == Project::TYPE_PROJECT){
                        $context->addViolationAt('type', 'Cannot add payable task to already started project');
                    }
                    break;
                case self::TYPE_ADJUSTMENT:
                    if($this->project->getType() == Project::TYPE_ESTIMATE){
                        $context->addViolationAt('type', 'Cannot add adjustment task to draft project');
                    }
                    break;
            }
        }
    }



    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getInvoicesTasks()
    {
        return $this->invoicesTasks;
    }

    /**
     * Returns true if task is done or canceled - no more work will be made
     *
     * @return bool
     */
    public function isFinished()
    {
        return in_array($this->status, [self::STATUS_CLOSED, self::STATUS_CANCELLED]);
    }

    /**
     * Add file
     *
     * @param TaskFile $file
     */
    public function addFile(TaskFile $file)
    {
        $file->setTask($this);
        $this->files->add($file);
    }

    /**
     * Remove file
     *
     * @param TaskFile $file
     */
    public function removeFile(TaskFile $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function isLocked()
    {
        $locked = false;

        $is_project_locked = $this->project !== null && $this->project->isProject();
        if($is_project_locked){
            if($this->isContracted()){
                $locked = true;
            } elseif($this->type == Task::TYPE_ADJUSTMENT) {
                foreach($this->getInvoicesTasks() as $invoice_task){
                    /** @var InvoiceTask $invoice_task */
                    if(!$invoice_task->getOrder()->getIsDraft()){
                        $locked = true;
                        break;
                    }
                }
            }
        }

        return $locked;
    }

    public function isParentValid(ExecutionContextInterface $context)
    {
        if($this->pid !== null){
            if($this->pid->getProject()->getId() !== $this->project->getId()){
                $context->addViolationAt('pid', 'Parent task has to be from the same project.');
            } else {
                $current_parent = $this->pid;
                while($current_parent !== null && $current_parent->getId() !== null){
                    if($current_parent->getId() == $this->getId()){
                        $context->addViolationAt('pid', 'This is creating bad circular dependency');
                        break;
                    } else {
                        $current_parent = $current_parent->getPid();
                    }
                }
            }

            if($this->dependency === null){
                $context->addViolationAt('dependency', 'Please select dependency');
            }
        }
    }

    /**
     * @param null $workingHours
     * @param null $startHour
     * @param null $endHour
     *
     * @return \DateTime|null
     */
    public function getEstimatedEnd($workingHours = null, $startHour = null, $endHour = null, $normalizationValue = null)
    {
        if($workingHours === null){
            $workingHours = $this->getProject()->getWorkingHours();

            if($workingHours === null){
                //fallback
                $workingHours = 8;
            }
        }

        if($startHour === null){
            $startHour = $this->getProject()->getStartHourAsInt();

            if($startHour === null){
                //fallback
                $startHour = 8;
            }
        }

        if($endHour === null){
            $endHour = $this->getProject()->getEndHourAsInt();

            if($endHour === null){
                //fallback
                $endHour = 16;
            }
        }

        if($this->estimatedEnd === false){
            $startDate = $this->getEstimatedStart($workingHours, $startHour, $endHour, $normalizationValue);

            if($this->dueDate !== null){
                $this->estimatedEnd = $this->dueDate;
            } elseif($startDate !== null && $this->estimatedTime !== null) {
                $end = clone $startDate;
                $duration = $this->getDurationInterval($workingHours);

                $end->add($duration);
                $this->normalizeDateForward($end, $startHour, $endHour);

                $this->estimatedEnd = $end;
            } else{
                $this->estimatedEnd = $normalizationValue;
            }
        }

        return $this->estimatedEnd;
    }

    /**
     * @param int  $workingHours
     * @param null $startHour
     * @param null $endHour
     * @param null $normalizationValue
     *
     * @return \DateTime|null
     */
    public function getEstimatedStart($workingHours = null, $startHour = null, $endHour = null, $normalizationValue = null)
    {
        if($workingHours === null){
            $workingHours = $this->getProject()->getWorkingHours();

            if($workingHours === null){
                //fallback
                $workingHours = 8;
            }
        }

        if($startHour === null){
            $startHour = $this->getProject()->getStartHourAsInt();

            if($startHour === null){
                //fallback
                $startHour = 8;
            }
        }

        if($endHour === null){
            $endHour = $this->getProject()->getEndHourAsInt();

            if($endHour === null){
                //fallback
                $endHour = 16;
            }
        }

        if($this->estimatedStart === false) {
            if ($this->startDate !== null) {
                $this->estimatedStart = $this->startDate;
            } elseif($this->dueDate !== null && $this->estimatedTime !== null) {
                $durationInterval = $this->getDurationInterval($workingHours);
                $start = clone $this->dueDate;
                $start->sub($durationInterval);

                $this->normalizeDateBackwards($start, $startHour, $endHour);

                $this->estimatedStart = $start;
            } elseif($this->estimatedTime !== null && $this->pid !== null) {
                /** @var TaskBase $parent */
                $parent = $this->pid;

                list($parent, $dependency) = $this->getTaskParentAndDependency($this);

                if($parent !== null && $dependency !== null) {
                    $durationInterval = $this->getDurationInterval($workingHours);
                    $lagInterval = $this->getLagInterval();

                    switch ($dependency) {
                        case TaskBase::DEPENDENCY_FINISH_TO_START:
                            $parent_end = $parent->getEstimatedEnd($workingHours);
                            if ($parent_end !== null) {
                                $start = clone $parent_end;
                                if ($lagInterval !== null) {
                                    $start->add($lagInterval);
                                    $this->normalizeLagDateForward($start, $startHour, $endHour);
                                }

                                $this->normalizeDateForward($start, $startHour, $endHour);
                                $this->estimatedStart = $start;
                            }

                            break;
                        case TaskBase::DEPENDENCY_FINISH_TO_FINISH:
                            $parent_end = $parent->getEstimatedEnd($workingHours);
                            if ($parent_end !== null) {
                                $start = clone $parent_end;

                                if ($lagInterval !== null) {
                                    $start->sub($lagInterval);
                                    $this->normalizeLagDateBackwards($start, $startHour, $endHour);
                                }
                                $this->estimatedEnd = clone $start;

                                $start->sub($durationInterval);

                                $this->normalizeDateBackwards($start, $startHour, $endHour);
                                $this->estimatedStart = $start;
                            }
                            break;
                        case TaskBase::DEPENDENCY_START_TO_START:
                            $parent_start = $parent->getEstimatedStart($workingHours);
                            if ($parent_start !== null) {
                                $start = clone $parent_start;
                                if ($lagInterval !== null) {
                                    $start->add($lagInterval);
                                    $this->normalizeLagDateForward($start, $startHour, $endHour);
                                }

                                $this->normalizeDateForward($start, $startHour, $endHour);
                                $this->estimatedStart = $start;
                            }
                            break;
//                    case TaskBase::DEPENDENCY_START_TO_FINISH:
//                        $parent_start = $parent->getEstimatedStart($workingHours);
//                        if($parent_start !== null){
//                            $start = clone $parent_start;
//
//                            if($lagInterval !== null){
//                                $start->sub($lagInterval);
//                                $this->normalizeLagDateBackwards($start, $startHour, $endHour);
//                            }
//
//                            $start->sub($durationInterval);
//
//                            $this->normalizeDateBackwards($start, $startHour, $endHour);
//                            $this->estimatedStart = $start;
//                        }
//                        break;
                    }
                }
            }

            //start not found, normalize it to null
            if($this->estimatedStart === false){
                $this->estimatedStart = $normalizationValue;
            }
        }

        return $this->estimatedStart;
    }

    protected function getTaskParentAndDependency(Task $task, $parentDependency = null)
    {
        $parent = $task->getPid();
        $dependency = $task->getDependency();

        if($parent->isCancelled()){
            $parentParent = $parent->getPid();
            if($parentParent !== null && $parentDependency === null){
                //only allow resolve dependency with one cancelled parent between
                return $this->getTaskParentAndDependency($parent, $dependency);
            } else {
                //no parent to relate, return empty
                return [null, null];
            }
        } else {
            if($parentDependency === null) {
                //no prev dependency to transform
                return [$parent, $dependency];
            } else {
                //prev dependency to transform
                $resolvedDependency = null;
                switch($dependency){
                    case Task::DEPENDENCY_FINISH_TO_START:
                        switch ($parentDependency){
                            case Task::DEPENDENCY_FINISH_TO_START:
                            case Task::DEPENDENCY_START_TO_START:
                                $resolvedDependency = Task::DEPENDENCY_FINISH_TO_START;
                                break;
                        }
                        break;
                    case Task::DEPENDENCY_START_TO_START:
                        if($parentDependency == Task::DEPENDENCY_START_TO_START){
                            $resolvedDependency = Task::DEPENDENCY_START_TO_START;
                        }
                        break;
                    case Task::DEPENDENCY_FINISH_TO_FINISH:
                        switch($parentDependency){
                            case Task::DEPENDENCY_FINISH_TO_FINISH:
                                $resolvedDependency = Task::DEPENDENCY_FINISH_TO_FINISH;
                                break;
                            case Task::DEPENDENCY_FINISH_TO_START:
                                $resolvedDependency = Task::DEPENDENCY_FINISH_TO_START;
                                break;
                        }
                        break;
                }

                return [$parent, $resolvedDependency];
            }
        }

    }

    /**
     * Gets closest not canceled parent and it dependency (can be different form original)
     *
     * @param $task
     */
    protected function getParentAndDependency(Task $task)
    {
        $parent = $task->getPid();
    }

    public function resetEstimations()
    {
        $this->estimatedStart = false;
        $this->estimatedEnd = false;
    }

    protected function getDurationInterval($working_hours)
    {
        if($this->estimatedTime !== null) {
            return $this->getInterval($this->estimatedTime, $working_hours);
        } else {
            return null;
        }
    }

    protected function getLagInterval()
    {
        if($this->lag !== null) {
            $interval_days  = floor($this->lag / 24);
            $interval_hours = $this->lag % 24;

            return new \DateInterval('P' . $interval_days . 'DT' . $interval_hours . 'H');
        } else {
            return null;
        }
    }

    protected function getInterval($hours, $working_hours)
    {
        $interval_days  = floor($hours / $working_hours);
        $interval_hours = $hours % $working_hours;
        $interval_minutes = round(fmod($hours, 1)*60);

        return new \DateInterval('P'.$interval_days.'DT'.$interval_hours.'H'.$interval_minutes.'M');
    }

    public function normalizeDateBackwards(\DateTime $date, $startHour, $endHour)
    {
        $taskStartHour = intval($date->format('H'));
        $minutes = $date->format('i');
        if($taskStartHour < $startHour){
            $hourDifference = $startHour - $taskStartHour;
            $date->sub(new \DateInterval('P1D'));
            $date->setTime($endHour-$hourDifference, $minutes);
        } elseif($taskStartHour > $endHour){
            $date->setTime($endHour - (24-$taskStartHour) - $startHour, $minutes);
        } elseif($taskStartHour == $endHour){
            $date->add(new \DateInterval('P1D'));
            $date->setTime($startHour, 0);
        }
    }

    public function normalizeDateForward(\DateTime $date, $startHour, $endHour)
    {
        $taskEndHour = intval($date->format('H'));
        $minutes = $date->format('i');

        if($taskEndHour > $endHour){
            $hourDifference = $taskEndHour - $endHour;
            $date->add(new \DateInterval('P1D'));
            $date->setTime($startHour+$hourDifference, $minutes);
        } elseif ($taskEndHour < $startHour){
            $date->setTime($startHour+$taskEndHour + (24 - $endHour), $minutes);
        }
    }

    protected function normalizeLagDateBackwards(\DateTime $date, $startHour, $endHour)
    {
        $dateHour = $date->format('H');
        if($dateHour > $endHour){
            $date->setTime($endHour, 0);
        } elseif($dateHour < $startHour){
            $date->sub(new \DateInterval('P1D'));
            $date->setTime($endHour, 0);
        }
    }

    public function normalizeLagDateForward(\DateTime $date, $startHour, $endHour)
    {
        $dateHour = $date->format('H');
        if($dateHour < $startHour){
            $date->setTime($startHour, 0);
        } elseif($dateHour > $endHour){
            $date->add(new \DateInterval('P1D'));
            $date->setTime($startHour, 0);
        }
    }

    public function setDueDate(DateTime $dueDate = null)
    {
        $this->resetEstimations();
        return parent::setDueDate($dueDate);
    }

    public function setEstimatedTime($estimatedTime)
    {
        $this->resetEstimations();
        return parent::setEstimatedTime($estimatedTime);
    }

    public function setStartDate(DateTime $startDate = null)
    {
        $this->resetEstimations();
        return parent::setStartDate($startDate);
    }

    /**
     * @return ContractCategory
     */
    public function getContractCategory()
    {
        return $this->contractCategory;
    }

    /**
     * @param ContractCategory $contractCategory
     */
    public function setContractCategory(ContractCategory $contractCategory = null)
    {
        $this->contractCategory = $contractCategory;
    }



}