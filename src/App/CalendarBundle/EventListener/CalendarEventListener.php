<?php

namespace App\CalendarBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use App\CalendarBundle\Entity\EventEntityWrapper;
use App\CalendarBundle\Form\SearchFilterType;
use App\PersonBundle\Entity\Person;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Filter\TasksQueryBuilderFilter;
use App\UserBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Parameter;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CalendarEventListener
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
    protected $twig;

    public function __construct(EntityManager $entityManager, RouterInterface $router, FormFactoryInterface $formFactory, SecurityContextInterface $securityContext, TwigEngine $twig)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->securityContext = $securityContext;
        $this->twig = $twig;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        $request = $calendarEvent->getRequest();

        $filters_form = $this->formFactory->create(new SearchFilterType(), array());
        $filters_form->submit($request);
        $filters = $filters_form->getData();

        /* Use this for events you create in your own bundle */
        $user = $this->securityContext->getToken()->getUser();

        if($filters['user'] instanceof User){
            $user = $filters['user'];
        }

        if($user instanceof User){
            $companyEvents = $this->entityManager->getRepository('AppCalendarBundle:EventEntity')->getInRange($startDate, $endDate, $user);

            foreach($companyEvents as $companyEvent) {
                /** @var \App\CalendarBundle\Entity\EventEntity $companyEvent */
                $event_start = $companyEvent->getStartDatetime();
                $event_end   = $companyEvent->getEndDatetime();
                if($event_start < $startDate){
                    continue;
                }

                // create an event with a start/end time, or an all day event
                if ($companyEvent->getAllDay() === false) {
                    $eventEntity = new EventEntityWrapper($companyEvent->getTitle(), $event_start, $event_end);
                } else {
                    $eventEntity = new EventEntityWrapper($companyEvent->getTitle(), $event_start, null, true);
                    $eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
                }

                $url = $this->router->generate('backend_calendar_event_show', array('id' => $companyEvent->getId()));
                $eventEntity->setUrl($url); // url to send user to when event label is clicked
                $eventEntity->setDetails($companyEvent->getDescription());
                $eventEntity->setId($companyEvent->getId()); // Set the id so we can update it

                //optional calendar event settings
                $eventEntity->setCssClass('cal-event');

                //finally, add the event to the CalendarEvent for displaying on the calendar
                $calendarEvent->addEvent($eventEntity);
            }
        }

        unset($filters['user']);

        //load tasks
        $tasks_repository = $this->entityManager->getRepository('AppTaskBundle:Task');
        $tasks_query_builder = $tasks_repository->getInRangeQueryBuilder($startDate, $endDate);

        $tasks_query_builder_filter = new TasksQueryBuilderFilter($tasks_repository);
        $tasks_query_builder_filter->filter($tasks_query_builder, $filters);

        $tasks = $tasks_query_builder->getQuery()->getResult();

        foreach($tasks as $task){
            /** @var Task $task */
            $taskStartDate = $task->getStartDate();
            $taskEndDate = $task->getDueDate();
            $taskUrl = $this->router->generate('backend_tasks_show', ['id' => $task->getId()]);
            $name = $task->getTracker()->__toString().' #'.$task->getId().' '.substr($task->getName(), 0, 30);
            $details = $this->twig->render('AppCalendarBundle:Default:taskDetails.html.twig', [
                'task' => $task
            ]);

            if($taskStartDate !== null && $taskEndDate !== null && $taskStartDate->format('d-m-Y') == $taskEndDate->format('d-m-Y')){
                //the same day
                $eventEntity = new EventEntityWrapper($name, $taskStartDate, $taskEndDate);
                $eventEntity->setCssClass('cal-task cal-task-start-end');
                $eventEntity->setUrl($taskUrl);
                $eventEntity->setDetails($details);

                $calendarEvent->addEvent($eventEntity);
            } else {
                //different days
                if($taskStartDate > $startDate && $taskStartDate < $endDate){
                    $eventEntity = new EventEntityWrapper($name, $taskStartDate, null, true);
                    $eventEntity->setCssClass('cal-task cal-task-start');
                    $eventEntity->setUrl($taskUrl);
                    $eventEntity->setDetails($details);

                    $calendarEvent->addEvent($eventEntity);
                }

                if($taskEndDate > $startDate && $taskEndDate < $endDate){
                    $eventEntity = new EventEntityWrapper($name, $taskEndDate, null, true);
                    $eventEntity->setCssClass('cal-task cal-task-end');
                    $eventEntity->setUrl($taskUrl);
                    $eventEntity->setDetails($details);

                    $calendarEvent->addEvent($eventEntity);
                }
            }


        }
    }
}