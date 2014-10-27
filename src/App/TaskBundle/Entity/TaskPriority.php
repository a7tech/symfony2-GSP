<?php

namespace App\TaskBundle\Entity;

use App\StatusBundle\Entity\BaseStatus;
use Doctrine\ORM\Mapping as ORM;


/**
 * TaskPriority
 *
 * @ORM\Table(name="task_priority")
 * @ORM\Entity(repositoryClass="App\TaskBundle\Entity\TaskPriorityRepository")
 */
class TaskPriority extends BaseStatus
{
}