<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 03.03.14
 * Time: 10:07
 */

namespace App\ProjectBundle\Transformer;


use App\CoreBundle\Twig\Extension\FormatterExtension;
use App\ProjectBundle\Entity\Category;
use App\ProjectBundle\Entity\GanttTask;
use App\ProjectBundle\Entity\Project;
use App\StatusBundle\Utils\StatusTranslator;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Entity\TaskBase;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

class GanttTaskTransformer
{

    protected $status_translator;

    protected $twigEngine;

    protected $entityManager;

    /**
     * @var FormatterExtension
     */
    protected $formatter;

    public function __construct(StatusTranslator $status_translator, TwigEngine $twigEngine, EntityManager $entityManager, FormatterExtension $formatter)
    {
        $this->status_translator = $status_translator;
        $this->twigEngine = $twigEngine;
        $this->entityManager = $entityManager;
        $this->formatter = $formatter;
    }

    /**
     * @param Category $category
     * @return GanttTask
     */
    public function transformCategory(Category $category)
    {
        $gantt_task = new GanttTask();
        $gantt_task->setId('c'.$category->getId());
        $gantt_task->setName($category->__toString());
        $gantt_task->setOpen(true);
        $gantt_task->setGanttType('category');

        return $gantt_task;
    }

    /**
     * @param Task $task
     *
     * @return GanttTask
     */
    public function transformTask(Task $task)
    {
        $start_normalization = $task->isContracted() ? new \DateTime() : null;
        $start_date = $task->getEstimatedStart(null, null, null, $start_normalization);
        $end_date = $task->getEstimatedEnd(null, null, null, $start_normalization);

        $gantt_task = new GanttTask();
        $gantt_task->setId('t'.$task->getId());
        $gantt_task->setTaskId($task->getId());
        $gantt_task->setName($task->getName().' (Id '.$task->getId().')');
        $gantt_task->setStartDate($start_date);
        $gantt_task->setEndDate($end_date);
        $gantt_task->setFormattedDuration($this->formatter->interval($task->getEstimatedTime(), $task->getProject()->getWorkingHours(), true));
        $gantt_task->setProgress($task->getDoneRatio());
        $gantt_task->setTracker($task->getTracker());
        $gantt_task->setStatus($this->status_translator->getStatusInfo(Task::STATUS_GROUP_NAME, $task->getStatus())['name']);
        $gantt_task->setAssigned($task->getAssignedTo());
        $gantt_task->setGanttType('task');
        $gantt_task->setOriginalTask($task);

        return $gantt_task;
    }

    public function transformCategoriesArray(array $categories, $scale_in_days = 1, GanttTask $base_task = null)
    {
        $tasks_data = [];
        $tasks_links = [];

        if($base_task !== null){
            $tasks_data[] = $base_task;
        }

        $this->transformSubcategoriesArray($tasks_data, $tasks_links, $categories, $base_task);

        $start_date = null;
        $end_date = null;

        $array_tasks = [];
        foreach($tasks_data as $gantt_task){
            /** @var GanttTask $gantt_task */
            $array_tasks[] = $gantt_task->toArray($this->twigEngine, $scale_in_days);

            $gantt_task_start_date = $gantt_task->getStartDate();
            $gantt_task_end_date = $gantt_task->getEndDate();

            if($gantt_task_start_date !== null){
                if($start_date === null || ($gantt_task_start_date < $start_date)){
                    $start_date = $gantt_task_start_date;
                }
            }

            if($gantt_task_end_date !== null) {
                if ($end_date === null  || $gantt_task_end_date > $end_date) {
                    $end_date = $gantt_task_end_date;
                }
            }
        }

        return [
            'data' => $array_tasks,
            'links' => $tasks_links,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }

    public function transformCategoriesToTasks(array $categories, $scale_in_days = 1, GanttTask $base_task = null)
    {
        $tasks_data = [];
        $tasks_links = [];

        if($base_task !== null){
            $tasks_data[] = $base_task;
        }

        $this->transformSubcategoriesArray($tasks_data, $tasks_links, $categories, $base_task);

        return $tasks_data;
    }

    protected function transformSubcategoriesArray(array &$tasks, array &$links, array &$categories, GanttTask $parent = null)
    {
        foreach($categories as $category){
            $gantt_category = $this->transformCategory($category['category']);

            if($parent !== null){
                $parent->addChild($gantt_category);

//                //link only base categories
//                if($parent->getParent() === null) {
//                    $links[] = [
//                        'id'     => count($links),
//                        'source' => $parent->getId(),
//                        'target' => $gantt_category->getId(),
//                        'type'   => 1
//                    ];
//                }
            }

            $tasks[] = $gantt_category;

            foreach($category['tasks'] as $task){
                /** @var Task $task */
                $gantt_task = $this->transformTask($task);
                $gantt_category->addChild($gantt_task);

                $tasks[] = $gantt_task;
                $task_dependency = $task->getPid();

                if($task_dependency !== null){
                    $dependency = $task->getDependency();
                    //normalize dependency to gantt chart format
                    if($dependency == 1){
                        $dependency = 2;
                    } elseif($dependency == 2){
                        $dependency = 1;
                    }

//                    $link = [];
//                    if($dependency == TaskBase::DEPENDENCY_START_TO_FINISH){ //not supported by dhtmlx Gantt chart
//                        //reverse source and target; change to finish to start
//                        $link = [
//                            'target' => $gantt_task->getId(),
//                            'source' => 't' . $task_dependency->getId(),
//                            'type'   => 0
//                        ];
//                    } else {
                        $link = [
                            'id' => 't' . $task_dependency->getId().'_'.$gantt_task->getId(),
                            'source' => 't' . $task_dependency->getId(),
                            'target' => $gantt_task->getId(),
                            'type'   => $dependency
                        ];
//                    }

                    $link['id'] = count($links);
                    $links[] = $link;
                }
//                else {
//                    //link tasks with categories
//                    $links[] = [
//                        'id'     => count($links),
//                        'source' => $gantt_category->getId(),
//                        'target' => $gantt_task->getId(),
//                        'type'   => 1
//                    ];
//                }
            }

            $this->transformSubcategoriesArray($tasks, $links, $category['children'], $gantt_category);
        }
    }

    public function transformProjectToArray(Project $project)
    {
        $categories = $project->getCategories(null, $this->entityManager, true, true, false);

        $gantt_project = new GanttTask();
        $gantt_project->setId('p');
        $gantt_project->setName($project->getName());
        $gantt_project->setGanttType('project');
        $gantt_project->areDatesPreserved(true);
        $gantt_project->setStartDate($project->getStartDate());

        $end_date = $project->getEndDate();
        if($end_date !== null){
            $gantt_project->setEndDate($end_date);
        }

        $gantt_chart_data = $this->transformCategoriesArray($categories, 1, $gantt_project);

        return $gantt_chart_data;
    }

    /**
     * @param Project $project
     *
     * @return GanttTask
     * @throws \Exception
     */
    public function transformProjectToTasks(Project $project, $cached = true)
    {
        $categories = $project->getCategories(null, $this->entityManager, $cached);

        $gantt_project = new GanttTask();
        $gantt_project->setId('p');
        $gantt_project->setName($project->getName());
        $gantt_project->setGanttType('project');
        $gantt_project->areDatesPreserved(true);
        $gantt_project->setStartDate($project->getStartDate());

        $end_date = $project->getEndDate();
        if($end_date !== null){
            $gantt_project->setEndDate($end_date);
        }

        $tasks = $this->transformCategoriesToTasks($categories, 1, $gantt_project);

        return $tasks[0];
    }
} 