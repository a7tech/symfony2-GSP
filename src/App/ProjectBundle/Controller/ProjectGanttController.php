<?php

namespace App\ProjectBundle\Controller;

use App\CoreBundle\Controller\Controller;
use App\ProjectBundle\Entity\GanttTask;
use App\ProjectBundle\Entity\Project;
use App\TaskBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;
use App\UserBundle\Annotation\RoleInfo;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @App\Route("/backend/project")
 * @RoleInfo(role="ROLE_BACKEND_PROJECT_GANTT_ALL", parent="ROLE_BACKEND_PROJECT_GANTT_ALL", desc="Projects gantt all access", module="Project Gantt")
 */
class ProjectGanttController extends Controller
{

    /**
     * getRepository
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProjectBundle:Project');
    }

    /**
     * getEntity
     *
     * @param int $id
     *
     * @return Project
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getEntityRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Project is not found');
        }

        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_GANTT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_GANTT_LIST", parent="ROLE_BACKEND_PROJECT_GANTT_ALL", desc="Project Gantt chart", module="Project Gantt")
     * @App\Route("/{pid}/gantt/", name="backend_project_gantt")
     * @App\Method("GET")
     */
    public function indexAction($pid = 0)
    {
        $project = $this->getEntity($pid);

        $gantt_chart_data = $this->getGanttData($project);

        $start_date = ($gantt_chart_data['start_date'] !== null) ? clone $gantt_chart_data['start_date'] : new \DateTime();
        $end_date   = ($gantt_chart_data['end_date'] !== null) ? clone $gantt_chart_data['end_date'] : new \DateTime('+1 Month');

        $chartMargin = new \DateInterval('P1W');

        //normalize start and end date
        $start_date->sub($chartMargin);
        $end_date->add($chartMargin);

        return $this->render('AppProjectBundle:Project:gantt.html.twig', array(
            'project'          => $project,
            'gantt_chart_data' => $gantt_chart_data,
            'start_date'       => $start_date,
            'end_date'         => $end_date,
            'working_days'     => array_values($project->getWorkingDays())
        ));
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_EDIT", parent="ROLE_BACKEND_TASK_ALL", desc="Edit Task", module="Task")
     * @App\Route("/{id}/gantt/update-task", name="backend_project_gantt_task_edit")
     * @App\Method("POST")
     */
    public function updateAction(Request $request, Project $project)
    {
        $data = $request->get('task');

        if (!isset($data) && !isset($data['id'])) {
            throw new NotFoundHttpException();
        }

        $task = $this->getRepository('AppTaskBundle:Task')->find($data['id']);

        if ($task === null || $task->getProject()->getId() != $project->getId()) {
            throw new NotFoundHttpException();
        }


        $entity_manager = $this->getEntityManager();
        $task_manager   = $this->get('task.manager');
        $changed        = false;

        if ($data['progress'] != $task->getDoneRatio()) {
            $task->setDoneRatio($data['progress']);
            $changed = true;
        }

        $start_date        = \DateTime::createFromFormat('D M d Y H:i:s e+', $data['start_date']);
        $duration_interval = new \DateInterval('PT' . intval($data['duration'] / 60) . 'H' . ($data['duration'] % 60) . 'M');
        $end_date          = clone $start_date;
        $end_date->add($duration_interval);

        $working_hours = $project->getWorkingHours();
        $start_hour    = $project->getStartHourAsInt();
        $end_hour      = $project->getEndHourAsInt();

        $estimated_start = $task->getEstimatedStart();
        $estimated_end   = $task->getEstimatedEnd();

        if ($estimated_start != $start_date && $estimated_end != $end_date) {
            $this->normalizeStartDate($start_date, $start_hour, $end_hour, function(\DateTime $start_date) use ($end_date, $duration_interval){
                $end_date = clone $start_date;
                $end_date->add($duration_interval);
            });

            if ($task->getStartDate() !== null || ($task->getStartDate() === null && $task->getDueDate() === null)) {
                $task->setStartDate($start_date);
            }

            //normalize end date
            $this->normalizeEndDate($end_date, $start_hour, $end_hour);

            if ($task->getDueDate() !== null) {
                $task->setDueDate($end_date);
            }


            $changed = true;
        } elseif ($estimated_start != $start_date) {
            //normalize start date
            $this->normalizeStartDate($start_date, $start_hour, $end_hour);

            if ($task->getStartDate() !== null) {
                $task->setStartDate($start_date);
            }

            $changed = true;
        } elseif ($estimated_end != $end_date) {
            $this->normalizeEndDate($end_date, $start_hour, $end_hour);

            if ($task->getDueDate() !== null) {
                $task->setDueDate($end_date);
            }

            $changed = true;
        }

        //duration in hours
        $duration_interval = $end_date->diff($start_date);
        $duration          = $duration_interval->d * $working_hours + $duration_interval->h + $duration_interval->m / 60;
        $task->setEstimatedTime($duration);

        if ($changed === true) {
            $task_manager->update($task);
            $entity_manager->flush();
        }

        $entity_manager->clear();

        $project    = $this->getRepository('AppProjectBundle:Project')->getById($project->getId());
        $gantt_data = $this->getGanttData($project);

        return new Response(json_encode($gantt_data));
    }

    protected function normalizeStartDate(\DateTime $date, $start_hour, $end_hour, $callback = null)
    {
        $start_date_hour = intval($date->format('H'));

        if ($start_date_hour < $start_hour || $start_date_hour > $end_hour) {
            if ($start_date_hour > $end_hour) {
                $date->add(new \DateInterval('P1D'));
            }
            $date->setTime($start_hour, 0);

            if(is_callable($callback)){
                call_user_func($callback, $date);
            }
        }
    }

    protected function normalizeEndDate(\DateTime $date, $start_hour, $end_hour)
    {
        $end_date_hour = intval($date->format('H'));

        if ($end_date_hour < $start_hour || $end_date_hour > $end_hour) {
            if ($end_date_hour > $end_hour) {
                $date->sub(new \DateInterval('P1D'));
            }

            $date->setTime($end_hour, 0);
        }
    }

    protected function getGanttData(Project $project)
    {
        $gantt_transformer = $this->get('app_project.gantt_chart.tasks_transformer');
        return $gantt_transformer->transformProjectToArray($project);
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_EDIT", parent="ROLE_BACKEND_TASK_ALL", desc="Edit Task", module="Task")
     * @App\Route("/{id}/gantt/delete-task/{task_id}", name="backend_project_gantt_task_delete")
     */
    public function deleteAction(Request $request, Project $project, $task_id)
    {

        $task = $this->getRepository('AppTaskBundle:Task')->find($task_id);

        if ($task === null || $task->getProject()->getId() != $project->getId()) {
            throw new NotFoundHttpException();
        }

        $task_manager = $this->get('task.manager');
        $task_manager->remove($task);

        $entity_manager = $this->getEntityManager();
        $entity_manager->flush();
        $entity_manager->clear();

        $project = $this->getRepository('AppProjectBundle:Project')->getById($project->getId());
        $gantt_data = $this->getGanttData($project);

        return new Response(json_encode($gantt_data));
    }
}