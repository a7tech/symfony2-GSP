<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 04.02.14
 * Time: 22:16
 */

namespace App\CalendarBundle\Entity;

use ADesigns\CalendarBundle\Entity\EventEntity as BaseEventEntity;

class EventEntityWrapper extends BaseEventEntity
{
    protected $details;

    /**
     * @param mixed $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['details'] = $this->details;

        return $array;
    }


} 