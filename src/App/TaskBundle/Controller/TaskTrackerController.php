<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\TaskBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use App\TaskBundle\Entity\TaskTracker;
use App\TaskBundle\Form\TaskTrackerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;


/**
 * @App\Route("/backend/settings/custom/task_tracker")
 * @RoleInfo(role="ROLE_BACKEND_TASK_TRACKER_ALL", parent="ROLE_BACKEND_TASK_TRACKER_ALL", desc="Task trackers all access", module="Task tracker")
 */
class TaskTrackerController extends Controller
{

    /**
     * getRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppTaskBundle:TaskTracker');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Project
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Task tracker is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_TRACKER_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TASK_TRACKER_LIST", parent="ROLE_BACKEND_TASK_TRACKER_ALL", desc="List Task trackers", module="Task tracker")
     * @App\Route("/", name="backend_settings_custom_task_tracker")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppTaskBundle:TaskTracker')->findAll();

        return $this->render('AppTaskBundle:TaskTracker:index.html.twig', array(
            'entities' => [],
        ));   

    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_LIST", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="List Project Milestone", module="Project Milestone")
     * @App\Route("/datatables", name="backend_task_tracker_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_tasks.filter.tasktrackerfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_TRACKER_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_TRACKER_CREATE", parent="ROLE_BACKEND_TASK_TRACKER_ALL", desc="Create Task tracker", module="Task tracker")
     * @App\Route("/create", name="backend_settings_custom_task_tracker_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new TaskTracker();
        $form = $this->createForm(new TaskTrackerType(), $entity);

        $request = $this->getRequest();        

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_task_tracker_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('AppTaskBundle:TaskTracker:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_TRACKER_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_TRACKER_EDIT", parent="ROLE_BACKEND_TASK_TRACKER_ALL", desc="Edit Task tracker", module="Task tracker")
     * @App\Route("/edit/{id}", name="backend_settings_custom_task_tracker_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {

        $entity = $this->getEntity($id); 
        $form = $this->createForm(new TaskTrackerType(), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_task_tracker_show', array('id' => $id)));
            }
        }

        return $this->render('AppTaskBundle:TaskTracker:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_TRACKER_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_TASK_TRACKER_SHOW", parent="ROLE_BACKEND_TASK_TRACKER_ALL", desc="Show Task tracker", module="Task tracker")
     * @App\Route("/show/{id}", name="backend_settings_custom_task_tracker_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppTaskBundle:TaskTracker:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     * @Secure(roles="ROLE_BACKEND_TASK_TRACKER_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_TRACKER_DELETE", parent="ROLE_BACKEND_TASK_TRACKER_ALL", desc="Delete Task tracker", module="Task tracker")
     * @App\Route("/delete/{id}", name="backend_settings_custom_task_tracker_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_task_tracker'));
    }

}
