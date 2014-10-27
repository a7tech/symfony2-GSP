<?php
namespace App\CalendarBundle\Entity;

use App\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class for holding a calendar event's details.
 * @ORM\Table(name="calendar_events") 
 * @ORM\Entity(repositoryClass="App\CalendarBundle\Entity\EventEntityRepository")
 */
class EventEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string Title/label of the calendar event.
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string URL Relative to current path.
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    protected $url;
    
    /**
     * @var string HTML color code for the bg color of the event label.
     * @ORM\Column(name="bgColor", type="string", nullable=true)
     */
    protected $bgColor;
    
    /**
     * @var string HTML color code for the foregorund color of the event label.
     * @ORM\Column(name="fgColor", type="string", nullable=true)
     */
    protected $fgColor;
    
    /**
     * @var string css class for the event label
     * @ORM\Column(name="cssClass", type="string", nullable=true)
     */
    protected $cssClass;
    
    /**
     * @var \DateTime DateTime object of the event start date/time.
     * @ORM\Column(name="startDatetime", type="datetime", nullable=true)
     */
    protected $startDatetime;
    
    /**
     * @var \DateTime DateTime object of the event end date/time.
     * @ORM\Column(name="endDatetime", type="datetime", nullable=true)
     */
    protected $endDatetime;
    
    /**
     * @var boolean Is this an all day event?
     * @ORM\Column(name="allDay", type="boolean", nullable=true)
     */
    protected $allDay = false;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    public function __construct($title = null, \DateTime $startDatetime = null, \DateTime $endDatetime = null, $allDay = false)
    {
        if(!is_null($title)){
            $this->title = $title;    
        }
        if(!is_null($startDatetime)){
            $this->startDatetime = $startDatetime;    
        }        
        if(!is_null($endDatetime)){
            $this->endDatetime = $endDatetime;    
        }        
        
        $this->setAllDay($allDay);
    }
    
    /**
     * Convert calendar event details to an array
     * 
     * @return array $event 
     */
    public function toArray()
    {
        $event = array();
        
        if ($this->id !== null) {
            $event['id'] = $this->id;
        }
        
        $event['title'] = $this->title;
        $event['start'] = $this->startDatetime->format("Y-m-d\TH:i:sP");
        
        if ($this->url !== null) {
            $event['url'] = $this->url;
        }
        
        if ($this->bgColor !== null) {
            $event['backgroundColor'] = $this->bgColor;
            $event['borderColor'] = $this->bgColor;
        }
        
        if ($this->fgColor !== null) {
            $event['textColor'] = $this->fgColor;
        }
        
        if ($this->cssClass !== null) {
            $event['className'] = $this->cssClass;
        }

        if ($this->endDatetime !== null) {
            $event['end'] = $this->endDatetime->format("Y-m-d\TH:i:sP");
        }
        
        $event['allDay'] = $this->allDay;
        $event['details'] = $this->details;

        return $event;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setTitle($title) 
    {
        $this->title = $title;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setBgColor($color)
    {
        $this->bgColor = $color;
    }
    
    public function getBgColor()
    {
        return $this->bgColor;
    }
    
    public function setFgColor($color)
    {
        $this->fgColor = $color;
    }
    
    public function getFgColor()
    {
        return $this->fgColor;
    }
    
    public function setCssClass($class)
    {
        $this->cssClass = $class;
    }
    
    public function getCssClass()
    {
        return $this->cssClass;
    }
    
    public function setStartDatetime(\DateTime $start)
    {
        $this->startDatetime = $start;
    }
    
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }
    
    public function setEndDatetime(\DateTime $end = null)
    {
        $this->endDatetime = $end;
    }
    
    public function getEndDatetime()
    {
        return $this->endDatetime;
    }
    
    public function setAllDay($allDay = false)
    {
        $this->allDay = (boolean) $allDay;
    }
    
    public function getAllDay()
    {
        return $this->allDay;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    /**
     * @param \App\UserBundle\Entity\User $user
     */
    public function setUser(User $user = null)
    {
        if($user !== null){
            $this->user = $user;
        }
    }

    /**
     * @return \App\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
