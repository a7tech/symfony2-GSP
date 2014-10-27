<?php

namespace App\TaskBundle\Manager;

use App\CoreBundle\Entity\ManagerInterface;
use App\InvoiceBundle\Entity\InvoiceTask;
use App\ProjectBundle\Entity\GanttTask;
use App\ProjectBundle\Entity\Project;
use App\ProjectBundle\Entity\ProjectRepository;
use App\ProjectBundle\Transformer\GanttTaskTransformer;
use App\TaskBundle\Entity\TaskBase;
use App\TaskBundle\Entity\Task;
use Doctrine\ORM\EntityManager;

class TaskManager extends TaskBaseManager
{
    /**
     * @var GanttTaskTransformer
     */
    protected $ganttTransformer;

    /**
     * @param GanttTaskTransformer $ganttTransformer
     */
    public function setGanttTransformer(GanttTaskTransformer $ganttTransformer)
    {
        $this->ganttTransformer = $ganttTransformer;
    }

    /**
     * Creates task
     *
     * @param Task $task
     */
    public function create(Task $task)
    {
        $taxes = $task->getProject()->getAccountProfile()->getTaxation();
        $task->setTaxes($taxes->toArray());
        if($task->getType() == TaskBase::TYPE_PAYABLE && $task->getProject()->getType() == Project::TYPE_ESTIMATE){
            $task->setIsContracted(true);
        }

        $this->normalizeOrdering($task, true);

        $this->updateProjectCache($task, false);


        $this->entityManager->persist($task);

        $this->showInvoice($task);
    }

    /**
     * Updates task
     *
     * @param Task $task
     */
    public function update(Task $task)
    {
        $this->normalizeOrdering($task, true);
        if($task->getProject()->getType() == Project::TYPE_ESTIMATE){
            $task->setIsContracted($task->getType() == TaskBase::TYPE_PAYABLE && $task->getStatus() !== TaskBase::STATUS_CANCELLED);
        }

        $this->updateProjectCache($task, false);

        $this->entityManager->persist($task);

        $this->showInvoice($task);
    }

    /**
     * Removes task
     *
     * @param Task $task
     */
    public function remove(Task $task)
    {
        $can_be_removed = true;
        if($task->getType() == TaskBase::TYPE_PAYABLE && $task->getProject()->getType() == Project::TYPE_PROJECT){
            $can_be_removed = false;
        } else if($task->getType() == Task::TYPE_ADJUSTMENT){
            foreach($task->getInvoicesTasks() as $invoice_task){
                /** @var InvoiceTask $invoice_task */
                if(!$invoice_task->getOrder()->getIsDraft()){
                    $can_be_removed = false;
                    break; //break foreach
                }
            }
        }

        if($can_be_removed){
            $this->normalizeOrdering($task, false);
            $this->entityManager->remove($task);
            $this->showInvoice($task);

            $this->updateProjectCache($task, false);
        } else {
            $this->cancelTask($task);
        }
    }

    public function cancelTask(Task $task)
    {
        $task->setStatus(TaskBase::STATUS_CANCELLED);
        $this->entityManager->persist($task);
        $this->showInvoice($task);
    }

    /**
     * Create Tasks
     *
     * @param $taskIds
     * @param $projectId
     * @return object
     */
    public function createTasks($taskIds, $projectId)
    {
        if (!empty($taskIds) && !empty($projectId)) {
            $tasks = $this->entityManager->getRepository('App\TaskBundle\Entity\TaskPreset')->findBy(["id"=>$taskIds], ['order' => 'ASC']);
            $project = $this->entityManager->getRepository('App\ProjectBundle\Entity\Project')->find($projectId);
            
            if ( !empty($tasks) && !empty($project) ){
                foreach ($tasks as $task){
                    $newTask = new Task();
                    $newTask->setTracker($task->getTracker());
                    $newTask->setName($task->getName());
                    $newTask->setDescription($task->getDescription());
                    $newTask->setTaskDescription($task->getTaskDescription());
                    $newTask->setCategory($task->getCategory());
                    $newTask->setPriority($task->getPriority());
                    $newTask->setStatus($task->getStatus());
                    $newTask->setType($task->getType());
                    $newTask->setCostType($task->getCostType());
                    $newTask->setUnitPrice($task->getUnitPrice());
                    $newTask->setUnitQuantity($task->getUnitQuantity());
                    $newTask->setProfit($task->getProfit());
                    $newTask->setLag($task->getLag());
                    $newTask->setIsMandatory($task->getIsMandatory());
                    $newTask->setEstimatedTime($task->getEstimatedTime());
                    $newTask->setEstimatedPrice($task->getEstimatedPrice());
                    $newTask->setDoneRatio($task->getDoneRatio());
                    $newTask->setDueDate($task->getDueDate());     
                    $newTask->setStartDate($task->getStartDate());
                    $newTask->setClosedAt($task->getClosedAt());
                    $newTask->setProject($project);
                    $newTask->setAssignedTo($task->getAssignedTo());
                    
                    $this->create($newTask);

                    //flush here in order to have proper order in tasks
                    $this->entityManager->flush();
                }
            }
        }
  
    }

    public function showInvoice(Task $task)
    {
        $project = $task->getProject();

        if($project->getInvoiceDeliveryType() == ProjectRepository::INVOICE_ON_DELIVERY && !$project->isEstimate() && $task->getType() == TaskBase::TYPE_PAYABLE){
            $category = $task->getContractCategory();
            $parent_category = $category->getParent();
            $root_category = $parent_category === null ? $category : $this->entityManager->getRepository('AppProjectBundle:ContractCategory')->getPath($category)[0];

            $project_categories = $project->getCategories(true, $this->entityManager, false);
            $show_invoice = true;
            foreach($project_categories[$root_category->getId()]['all_tasks'] as $category_task){
                /** @var Task $category_task */
                if(!$category_task->isFinished()){
                    $show_invoice = false;
                    break;
                }
            }

            if($show_invoice === true){
                $invoices_repository = $this->entityManager->getRepository('AppInvoiceBundle:SaleOrder');
                $invoice = $invoices_repository->getByProjectCategory($project, $root_category);

                if($invoice !== null){
                    $invoice->setIsVisible(true);
                    $this->entityManager->persist($invoice);
                }
            }
        }
    }

    /**
     * Fixes tasks ordering in project category
     *
     * @param Task $task
     * @param bool $update true on add or update, false on delete
     */
    protected function normalizeOrdering(Task $task, $update = true)
    {
        $project = $task->getProject();
        $category = $task->getCategory();
        $tasks_repository = $this->getRepository();
        $tasks = $tasks_repository->getByCategory($category, $project);

        $this->normalizeItemsOrdering($task, $tasks, $update);
    }

    protected function getRepository()
    {
        return $this->entityManager->getRepository('AppTaskBundle:Task');
    }

    protected function updateProjectCache(Task $task, $cached = true)
    {
        $project = $task->getProject();
        $ganttProject = $this->ganttTransformer->transformProjectToTasks($project, $cached);

        $project->setProgress($ganttProject->getProgress());
        $project->setRealStartDate($ganttProject->getStartDate());
        $project->setRealEndDate($ganttProject->getEndDate());

        $this->entityManager->persist($project);
    }
}