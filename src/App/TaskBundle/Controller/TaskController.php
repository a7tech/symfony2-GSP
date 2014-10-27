<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\TaskBundle\Controller;

use App\CoreBundle\Controller\Controller;
use App\ProjectBundle\Entity\Project;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Entity\TaskRepository;
use App\TaskBundle\Form\TaskType;

use App\TaskBundle\Form\TasksFiltersType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;
use Doctrine\ORM\EntityRepository;

/**
 * @App\Route("/backend/tasks")
 * @RoleInfo(role="ROLE_BACKEND_TASK_ALL", parent="ROLE_BACKEND_TASK_ALL", desc="Tasks all access", module="Task")
 */
class TaskController extends Controller
{

    /**
     * getRepository
     *
     * @return TaskRepository
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppTaskBundle:Task');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getEntityRepository()->getById($id);
        if ($entity === null) {
            throw $this->createNotFoundException('Project task is not found');
        }

        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TASK_LIST", parent="ROLE_BACKEND_TASK_ALL", desc="List Tasks", module="Task")
     * @App\Route("/", name="backend_tasks")
     * @App\Route("/project/{project_id}/", name="backend_tasks_project")
     * @App\Method("GET")
     */
    public function indexAction($project_id = null)
    {
        $project = null;
        if ($project_id !== null) {
            $project = $this->get('doctrine.orm.entity_manager')->getRepository('AppProjectBundle:Project')->getById($project_id);
        }

        $filters = [];
        if($project !== null){
            $filters['project']['value'] = [$project];
        }

        $filters_form = $this->createForm(new TasksFiltersType(), $filters);
       


        return  $this->render('AppTaskBundle:Task:index.html.twig', array(
                    'entities' => [],
                    'filters_form' => $filters_form->createView(),
                    'project'  => $project
        ));

     

    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TASK_LIST", parent="ROLE_BACKEND_TASK_ALL", desc="List Tasks", module="Task")
     * @App\Route("/datatables", name="backend_task_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_tasks.filter.taskfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_CREATE", parent="ROLE_BACKEND_TASK_ALL", desc="Create Task", module="Task")
     * @App\Route("/create", name="backend_tasks_create")
     * @App\Route("/project/{project_id}/create", name="backend_tasks_project_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction($project_id = null)
    {
        $entity = new Task();

        $project   = null;
        $startDate = new \DateTime();
        if ($project_id !== null) {
            /** @var Project $project */
            $project = $this->get('doctrine.orm.entity_manager')->getRepository('AppProjectBundle:Project')->getById($project_id);

            if ($project === null) {
                throw new \NotFoundHttpException('Project doesn\'t exist');
            }

            $entity->setProject($project);

            if ($project->getStartDate() !== null && $project->getStartDate() > $startDate) {
                $startDate = clone $project->getStartDate();
                $startDate->setTime($project->getStartHourAsInt(), 0);
            } else {
                $current_start_hour = intval($startDate->format('H'));
                $project_end_hour   = $project->getEndHourAsInt();
                if ($current_start_hour > $project_end_hour || ($current_start_hour == $project_end_hour && $startDate->format('i') > 0)) {
                    $startDate->add(new \DateInterval('P1D'));
                    $startDate->setTime($project->getStartHourAsInt(), 0);
                }
            }
        }

        $entity->setStartDate($startDate);
        $entity->setCostType(Task::COST_TYPE_TIME);
        $entity->setType($project === null || $project->isEstimate() ? Task::TYPE_PAYABLE : Task::TYPE_ADJUSTMENT);

        $form    = $this->createForm('task', $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $tasks_manager = $this->get('task.manager');
                $tasks_manager->create($entity);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirect($this->generateUrl('backend_tasks_project_show', array('id' => $entity->getId(), 'project_id' => $entity->getProject()->getId())));
            }
        }

        return $this->render('AppTaskBundle:Task:create.html.twig', array(
                    'entity'  => $entity,
                    'project' => $project,
                    'form'    => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_EDIT", parent="ROLE_BACKEND_TASK_ALL", desc="Edit Task", module="Task")
     * @App\Route("/edit/{id}", name="backend_tasks_edit")
     * @App\Route("/project/{project_id}/edit/{id}", name="backend_tasks_project_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id, $project_id = null)
    {
        $entity = $this->getEntity($id);
        $form   = $this->createForm('task', $entity);

        $project = $entity->getProject();

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {

            $oldFiles = $entity->getFiles()->toArray();

            $form->submit($request);

            if ($form->isValid()) {

                $removeFiles = array_udiff($oldFiles, $entity->getFiles()->toArray(), function($fileA, $fileB) {
                    if ($fileA->getId() == $fileB->getId())
                        return 0;
                    return $fileA->getId() > $fileB->getId() ? 1 : -1;
                }
                );

                $em = $this->getDoctrine()->getManager();

                foreach ($removeFiles as $file) {
                    $entity->removeFile($file);
                    $em->remove($file);
                }

                $tasks_manager = $this->get('task.manager');
                $tasks_manager->update($entity);

                $em->flush();

                return $this->redirect($this->generateUrl('backend_tasks_project_show', array('id' => $id, 'project_id' => $entity->getProject()->getId())));
            }
        } else {
            $this->addNotificationAboutCancelledParent($entity);
        }

        return $this->render('AppTaskBundle:Task:create.html.twig', array(
                    'entity'  => $entity,
                    'project' => $project,
                    'form'    => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_TASK_SHOW", parent="ROLE_BACKEND_TASK_ALL", desc="Show Task", module="Task")
     * @App\Route("/show/{id}", name="backend_tasks_show")
     * @App\Route("/project/{project_id}/show/{id}", name="backend_tasks_project_show")
     * @App\Method("GET")
     */
    public function showAction($project_id = 0, $id = 0)
    {
        $entity  = $this->getEntity($id);
        $project = $entity->getProject();

        $this->addNotificationAboutCancelledParent($entity);

        return $this->render('AppTaskBundle:Task:show.html.twig', array(
                    'entity'  => $entity,
                    'project' => $project,
        ));
    }

    protected function addNotificationAboutCancelledParent(Task $entity)
    {
        $parent = $entity->getPid();
        if ($parent !== null && $parent->isCancelled()) {
            $this->addAdminMessage($this->get('translator')->trans('dependency_on_cancelled_task_warning', [], 'Tasks'), 'warning');
        }
    }

    /**
     * deleteAction
     * @Secure(roles="ROLE_BACKEND_TASK_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_DELETE", parent="ROLE_BACKEND_TASK_ALL", desc="Delete Task", module="Task")
     * @App\Route("/delete/{id}", name="backend_tasks_delete")
     * @App\Route("/project/{project_id}/delete/{id}", name="backend_tasks_project_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em            = $this->getDoctrine()->getManager();
        $tasks_manager = $this->get('task.manager');
        $tasks_manager->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_tasks_project', ['project_id' => $entity->getProject()->getId()]));
    }

    /**
     * set order
     * @Secure(roles="ROLE_BACKEND_TASK_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_EDIT", parent="ROLE_BACKEND_TASK_ALL", desc="Edit Task", module="Task")
     * @App\Route("/set-order", name="backend_tasks_set_order")
     * @App\Method("POST")
     */
    public function setOrderAction(Request $request)
    {
        $id   = $request->get('id');
        $task = $this->getEntity($id);

        $tasks_manager = $this->get('task.manager');

        $order = $request->request->getInt('order');
        $task->setOrder($order);
        $tasks_manager->update($task);
        $this->get('doctrine.orm.entity_manager')->flush();

        return new Response(json_encode(['success' => true]));
    }

}
