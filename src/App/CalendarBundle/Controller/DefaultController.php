<?php

namespace App\CalendarBundle\Controller;

use App\CalendarBundle\Form\SearchFilterType;
use App\CoreBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Calendar controller.
 *
 * @RoleInfo(role="ROLE_BACKEND_CALENDAR_ALL", parent="ROLE_BACKEND_ALL_SETTINGS", desc="All calendar access", module="Calendar")
 * @Route("/backend/calendar")
 */
class DefaultController extends Controller
{
	/**
     * Lists all event entities as a calendar.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_SHOW", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Show calendar", module="Calendar")
     * @Route("/", name="backend_calendar")
     * @Route("/project/{project_id}", name="backend_project_calendar")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($project_id = null)
    {
        $project = null;
        if($project_id !== null){
            $project = $this->getRepository('AppProjectBundle:Project')->getById($project_id);
        }

        $filters = [];
        if($project !== null){
            $filters['project']['value'] = [$project];
        }

      

        $filters_form = $this->createForm(new SearchFilterType(), $filters);
      
        return array(
            'filters_form' => $filters_form->createView(),
            'project' => $project
        );
    }

    /**
     * Lists all event entities as a calendar.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_SHOW", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Show calendar", module="Calendar")
     * @Route("/day", name="backend_calendar_day")
     * @Method("GET")
     * @Template("AppCalendarBundle:Default:index.html.twig")
     */
    public function dayAction()
    {
        $filters_form = $this->createForm(new SearchFilterType(), []);

        return array(
            'filters_form' => $filters_form->createView(),
            'project' => null
        );
    }
}
