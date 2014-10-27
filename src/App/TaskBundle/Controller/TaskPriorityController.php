<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\TaskBundle\Controller;

use App\TaskBundle\Entity\TaskPriority;
use App\TaskBundle\Form\TaskPriorityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\ORM\EntityRepository;
use App\UserBundle\Annotation\RoleInfo;


/**
 * @App\Route("/backend/settings/custom/task_priority")
 * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_ALL", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="Task priorities all access", module="Task priority")
 */
class TaskPriorityController extends Controller
{

    /**
     * getRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppTaskBundle:TaskPriority');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return TaskPriority
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Task Priority is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRIORITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_LIST", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="List Task priorities", module="Task priority")
     * @App\Route("/", name="backend_settings_custom_task_priority")
     * @App\Method("GET")
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('AppTaskBundle:TaskPriority')->findAll();

        return $this->render('AppTaskBundle:TaskPriority:index.html.twig', array(
            'entities' => []
        ));   

    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRIORITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_LIST", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="List Task priorities", module="Task priority")
     * @App\Route("/datatables", name="backend_task_priority_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_tasks.filter.taskpriorityfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRIORITY_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_CREATE", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="Create Task priority", module="Task priority")
     * @App\Route("/create", name="backend_settings_custom_task_priority_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new TaskPriority();
        $form = $this->createForm(new TaskPriorityType(), $entity);
        $request = $this->getRequest();        

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_task_priority_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('AppTaskBundle:TaskPriority:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRIORITY_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_EDIT", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="Edit Task priority", module="Task priority")
     * @App\Route("/edit/{id}", name="backend_settings_custom_task_priority_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new TaskPriorityType(), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_task_priority_edit', array('id' => $id)));
            }
        }

        return $this->render('AppTaskBundle:TaskPriority:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRIORITY_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_SHOW", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="Show Task priority", module="Task priority")
     * @App\Route("/show/{id}", name="backend_settings_custom_task_priority_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppTaskBundle:TaskPriority:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     * @Secure(roles="ROLE_BACKEND_TASK_PRIORITY_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRIORITY_DELETE", parent="ROLE_BACKEND_TASK_PRIORITY_ALL", desc="Delete Task priority", module="Task priority")
     * @App\Route("/delete/{id}", name="backend_settings_custom_task_priority_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_task_priority'));
    }

}
