<?php

namespace App\TaskBundle\Entity;

use App\StatusBundle\Entity\BaseStatus;
use Doctrine\ORM\Mapping as ORM;


/**
 * Tracker
 *
 * @ORM\Table(name="task_tracker")
 * @ORM\Entity(repositoryClass="App\TaskBundle\Entity\TaskTrackerRepository")
 */
class TaskTracker extends BaseStatus
{
}