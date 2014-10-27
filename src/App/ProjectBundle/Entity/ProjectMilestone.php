<?php

namespace App\ProjectBundle\Entity;

use App\StatusBundle\Entity\BaseStatus;
use App\StatusBundle\Entity\StatusesProviderInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * ProjectMilestone
 *
 * @ORM\Table(name="project_milestone")
 * @ORM\Entity(repositoryClass="App\ProjectBundle\Entity\ProjectMilestoneRepository")
 */
class ProjectMilestone extends BaseStatus implements StatusesProviderInterface
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_LOST = 2;
    const STATUS_WON = 3;
    const STATUS_GROUP_NAME = 'project_milestone_statuses';

    protected static $statuses = [
        self::STATUS_NEW            => 'new',
        self::STATUS_IN_PROGRESS    => 'in progress',
        self::STATUS_LOST           => 'lost',
        self::STATUS_WON            => 'won'
    ];

	/**
     * @var integer
     * @ORM\Column(name="value", type="integer", length=11)
     */
    protected $value;

    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=8, nullable=true)
     */
    protected $color;


    // Static methods
    public static function getStatuses()
    {
        return self::$statuses;
    }

    public static function getStatusesArray()
    {
        return [
            self::STATUS_GROUP_NAME    => self::getStatuses(),
        ];
    }

    /**
     * @param integer $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @var integer
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}