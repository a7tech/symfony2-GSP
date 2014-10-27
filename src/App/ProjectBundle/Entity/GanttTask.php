<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 28.02.14
 * Time: 16:53
 */

namespace App\ProjectBundle\Entity;


use App\TaskBundle\Entity\Task;
use App\TaskBundle\Entity\TaskTracker;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Validator\Constraints\DateTime;

class GanttTask
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var integer
     */
    protected $taskId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var integer
     */
    protected $duration;

    /**
     * @var string
     */
    protected $formattedDuration;

    /**
     * @var float
     */
    protected $progress = 0;

    /**
     * @var boolean
     */
    protected $open = true;

    /**
     * @var array
     */
    protected $users;

    /**
     * @var integer
     */
    protected $priority;

    /**
     * @var TaskTracker
     */
    protected $tracker;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var GanttTask
     */
    protected $parent;

    /**
     * @var ArrayCollection
     */
    protected $children;

    protected $ganttType;

    /**
     * @var Task
     */
    protected $originalTask;

    protected $assigned;

    protected $preserveDates = false;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->assigned = new ArrayCollection();
    }

    public function toArray(TwigEngine $template_engine, $scaleInDays = 1, $working_hours = 8)
    {
        $startDate = $this->startDate !== null ? $this->startDate : new \DateTime();

        $interval = ($this->endDate !== null) ? $this->endDate->diff($startDate) : null;
        $duration = $interval !== null ? ($interval->days*24 + $interval->h)*60 + $interval->m : 0;

        $progress = $this->getProgress();

        if($this->endDate !== null && $duration > 0){
            $endDate = $this->endDate;
        } else {
            $endDate = clone $startDate;
        }

        $array = [
            'id' => $this->id,
            'task_id' => $this->taskId,
            'start_date' => $startDate->format('d-m-Y H:i'),
            'end_date' => $endDate->format('d-m-Y H:i'),
            'original_start_date' => $this->startDate !== null ? $this->startDate->format('d-m-Y') : null,
            'original_end_date' => $this->endDate !== null ? $this->endDate->format('d-m-Y') : null,
            'text' => $this->getName(),
            'progress' => $progress,
            'original_progress' => $progress,
            'duration' => $duration,
            'formatted_duration' => $this->formattedDuration,
            'open' => $this->getOpen(),
            'tracker' => $this->tracker !== null ? (string)$this->tracker : null,
            'status' => $this->getStatus(),
            'task_name' => $this->originalTask !== null ? $this->originalTask->getName() : null,
            'gantt_type' => $this->ganttType,
            'type' => $this->getTaskTypeLetter(),
            'assigned' => [],
            'tooltip' => $template_engine->render('AppProjectBundle:Project:taskTooltip.html.twig', [
                    'is_task' => $this->ganttType == 'task',
                    'task' => $this->originalTask,
                    'gantt_task' => $this
                ])
        ];

        foreach($this->assigned as $assigned_person){
            $array['assigned'][] = (string)$assigned_person;
        }

        if($this->parent !== null){
            $array['parent'] = $this->parent->getId();
        }

        return $array;
    }

    protected function getTaskTypeLetter()
    {
        if($this->originalTask !== null){
            return strtoupper(substr($this->originalTask->getTypeName(), 0, 1));
        }

        return null;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param \DateTime $endDate
     * @param bool      $forceParentRefresh
     */
    public function setEndDate($endDate, $forceParentRefresh = true)
    {
        $this->endDate = $endDate;

        if($this->parent !== null && $forceParentRefresh){
            $this->parent->refreshEndDate();
        }
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @param string $name
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
     * @param boolean $open
     */
    public function setOpen($open)
    {
        $this->open = $open;
    }

    /**
     * @return boolean
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @param \App\ProjectBundle\Entity\GanttTask $parent
     */
    public function setParent(GanttTask $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return \App\ProjectBundle\Entity\GanttTask
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param float $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return float
     */
    public function getProgress()
    {
        if($this->progress === null){
            $progress_sum = 0.0;

            foreach($this->children as $child){
                /** @var GanttTask $child */
                $progress_sum += $child->getProgress();
            }

            $this->progress = $progress_sum/$this->children->count();
        }

        return $this->progress;
    }

    /**
     * @param \DateTime $startDate
     * @param bool      $forceParentRefresh
     */
    public function setStartDate($startDate, $forceParentRefresh = true)
    {
        $this->startDate = $startDate;

        if($this->parent !== null && $forceParentRefresh){
            $this->parent->refreshStartDate();
        }
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param array $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children->clear();

        foreach($children as $child){
            $this->addChild($child);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }


    public function addChild(GanttTask $task)
    {
        $this->progress = null;
        $task->setParent($this);
        $this->children[] = $task;

        if($this->startDate === null || ($task->getStartDate() !== null && $task->getStartDate() < $this->startDate)){
            $this->setStartDate($task->getStartDate());

            if($this->parent !== null){
                $this->parent->refreshStartDate();
            }
        }

        if($this->endDate === null || ($task->getEndDate() !== null && $task->getEndDate() > $this->endDate)){
            $this->setEndDate($task->getEndDate());

            if($this->parent !== null){
                $this->parent->refreshEndDate();
            }
        }
    }

    public function removeChild(GanttTask $task)
    {
        $this->progress = null;
        $task->setParent(null);
        $this->children->removeElement($task);

        if($task->getStartDate() == $this->getStartDate()){
            $this->refreshStartDate();
        }

        if($task->getEndDate() == $this->getEndDate()){
            $this->refreshEndDate();
        }
    }

    /**
     * @param \App\TaskBundle\Entity\TaskTracker $tracker
     */
    public function setTracker(TaskTracker $tracker = null)
    {
        $this->tracker = $tracker;
    }

    /**
     * @return \App\TaskBundle\Entity\TaskTracker
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $assigned
     */
    public function setAssigned($assigned)
    {
        $this->assigned = $assigned;
    }

    /**
     * @return mixed
     */
    public function getAssigned()
    {
        return $this->assigned;
    }

    /**
     * @return mixed
     */
    public function getGanttType()
    {
        return $this->ganttType;
    }

    /**
     * @param mixed $ganttType
     */
    public function setGanttType($ganttType)
    {
        $this->ganttType = $ganttType;
    }

    /**
     * @return mixed
     */
    public function getOriginalTask()
    {
        return $this->originalTask;
    }

    /**
     * @param mixed $originalTask
     */
    public function setOriginalTask(Task $originalTask)
    {
        $this->originalTask = $originalTask;
    }

    public function refreshStartDate()
    {
        if($this->preserveDates === false){
            $this->startDate = null;
        }

        foreach($this->children as $child){
            /** @var GanttTask $child */
            $childStartDate = $child->getStartDate();
            if($childStartDate !== null && ($this->startDate === null || $childStartDate < $this->startDate)){
                $this->startDate = $childStartDate;
            }
        }

        if($this->parent !== null){
            $this->parent->refreshStartDate();
        }
    }

    public function refreshEndDate()
    {
        if($this->preserveDates === false){
            $this->endDate = null;
        }

        foreach($this->children as $child){
            /** @var GanttTask $child */
            $childEndDate = $child->getEndDate();

            if($childEndDate !== null && ($this->endDate === null || $childEndDate > $this->endDate)){
                $this->endDate = $childEndDate;
            }
        }

        if($this->parent !== null){
            $this->parent->refreshEndDate();
        }
    }

    public function refreshDates()
    {
        $this->refreshStartDate();
        $this->refreshEndDate();
    }

    /**
     * @return boolean
     */
    public function isPreserveDates()
    {
        return $this->preserveDates;
    }

    /**
     * @param boolean $preserveDates
     */
    public function areDatesPreserved($preserveDates)
    {
        $this->preserveDates = $preserveDates;
    }

    /**
     * @return string
     */
    public function getFormattedDuration()
    {
        return $this->formattedDuration;
    }

    /**
     * @param string $formattedDuration
     */
    public function setFormattedDuration($formattedDuration)
    {
        $this->formattedDuration = $formattedDuration;
    }
}