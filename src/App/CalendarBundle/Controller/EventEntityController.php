<?php

namespace App\CalendarBundle\Controller;

use App\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\CalendarBundle\Entity\EventEntity;
use App\CalendarBundle\Form\EventEntityType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;
/**
 * EventEntity controller.
 *
 * @Route("/backend/calendar/event")
 */
class EventEntityController extends Controller
{

    /**
     * getCategoryRepository
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppCalendarBundle:EventEntity');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getEntityRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Product is not found');
        }
        return $entity;
    }

    /**
     * Lists all EventEntity entities.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_SHOW", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Show calendar", module="Calendar")
     * @Route("/", name="backend_calendar_event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppCalendarBundle:EventEntity')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new EventEntity entity.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_CREATE", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Create calendar", module="Calendar")
     * @Route("/", name="backend_calendar_event_create")
     * @Method("POST")
     * @Template("AppCalendarBundle:EventEntity:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EventEntity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if($entity->getUser() === null){
                $entity->setUser($this->getCurrentUser());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_calendar', array()));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a EventEntity entity.
    *
    * @param EventEntity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EventEntity $entity)
    {
        $form = $this->createForm(new EventEntityType(), $entity, array(
            'action' => $this->generateUrl('backend_calendar_event_create'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal')
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-default'),
        ));

        return $form;
    }

    /**
     * Displays a form to create a new EventEntity entity.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_CREATE", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Create calendar", module="Calendar")
     * @Route("/new/{date}", name="backend_calendar_event_new", options={"expose"=true}, defaults={"date" = ""})
     * @Method("GET")
     * @Template()
     */
    public function newAction($date = null)
    {
        if($date == ''){
            // No date given, dedault to the current date
            $givendate = new \DateTime();
            $enddate = new \DateTime();
        }else{
           /**
             * Parse the date with the e+ syntax, so extended timezones work as well
             * http://stackoverflow.com/questions/13421635/failed-to-parse-time-string-at-position-41-i-double-timezone-specification
             */
            $givendate = \DateTime::createFromFormat('D M d Y H:i:s e+', $date);
            $enddate = \DateTime::createFromFormat('D M d Y H:i:s e+', $date);
        }
        $enddate = $enddate->add(new \DateInterval('PT1H'));
        $entity = new EventEntity(null, $givendate, $enddate);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EventEntity entity.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_SHOW", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Show calendar", module="Calendar")
     * @Route("/show/{id}", name="backend_calendar_event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCalendarBundle:EventEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventEntity entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing EventEntity entity.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_EDIT", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Edit calendar", module="Calendar")
     * @Route("/edit/{id}/", name="backend_calendar_event_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCalendarBundle:EventEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventEntity entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a EventEntity entity.
    *
    * @param EventEntity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EventEntity $entity)
    {
        $form = $this->createForm(new EventEntityType(), $entity, array(
            'action' => $this->generateUrl('backend_calendar_event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EventEntity entity.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_EDIT", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Edit calendar", module="Calendar")
     * @Route("/{id}", name="backend_calendar_event_update")
     * @Method("PUT")
     * @Template("AppCalendarBundle:EventEntity:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCalendarBundle:EventEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventEntity entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->getUser() === null){
                $entity->setUser($this->getCurrentUser());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_calendar_event_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing EventEntity entity.
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_EDIT", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Edit calendar", module="Calendar")
     * @Route("/js/{id}", name="backend_calendar_event_update_js", options={"expose"=true})
     * @Method("PUT")
     * @Template("AppCalendarBundle:EventEntity:edit.html.twig")
     */
    public function updatejsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCalendarBundle:EventEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventEntity entity.');
        }

        $event = $request->request->get('event');
        $givendate = \DateTime::createFromFormat('D M d Y H:i:s e+', $event['start']);
        $entity->setStartDateTime($givendate);

        if($event['allDay'] != "false"){
            $entity->setAllDay(true);    
        }else{
            $givendate = \DateTime::createFromFormat('D M d Y H:i:s e+', $event['end']);
            $entity->setEndDateTime($givendate);    
        }

        $em->flush();

        $response = new Response();
        $response->setStatusCode('204');
        return $response;
 
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_CALENDAR_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_CALENDAR_DELETE", parent="ROLE_BACKEND_CALENDAR_ALL", desc="Delete calendar", module="Calendar")
     * @Route("/delete/{id}", name="backend_calendar_event_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_calendar'));
    }
}