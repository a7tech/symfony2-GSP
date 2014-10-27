<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-07-24
 * Time: 21:44
 */

namespace App\InvoiceBundle\Form\Type;


use App\TaskBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

abstract class TaskSelectType extends AbstractType
{
    protected $entityManager;

    protected $tasks;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $tasks_data = [];

        foreach($this->tasks as $task){
            /** @var Task $task */
            $taxes = [];
            foreach($task->getTaxes() as $tax){
                $taxes[] = $tax->getId();
            }

            $tasks_data[$task->getId()] = array_merge([
                'name' => $task->getName(),
                'net' => $task->getNetPrice(true),
                'taxes' => $taxes,
                'description' => $task->getTaskDescription()
            ], $this->addAdditionalTaskInfo($task));
        }

        $view->vars['attr']['data-tasks-info'] = json_encode($tasks_data);
    }

    protected function addAdditionalTaskInfo(Task $task)
    {
        return [];
    }
} 