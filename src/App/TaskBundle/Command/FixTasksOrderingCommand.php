<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.03.14
 * Time: 00:05
 */

namespace App\TaskBundle\Command;


use App\TaskBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\Tests\ORM\Functional\Ticket\Entity;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixTasksOrderingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:tasks:fix-order')
            ->setDescription('Fixes order of tasks in application');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $entity_manager */
        $entity_manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $projects = $entity_manager->getRepository('AppProjectBundle:Project')->getAll();

        $tasks_repository = $entity_manager->getRepository('AppTaskBundle:Task');

        foreach($projects as $project){
            //order tasks by project
            $projects_tasks_query_builder = $tasks_repository->getProjectTasksQueryBuilder($project);
            $projects_tasks_query_builder->join($tasks_repository->column('category'), 'Category')
                ->add('select', 'Category', true)
                ->orderBy($tasks_repository->column('order'), 'ASC');

            $tasks = $projects_tasks_query_builder->getQuery()->getResult();

            $this->orderTasks($tasks, $entity_manager);

            $entity_manager->flush();
            $entity_manager->clear('AppTaskBundle:Task');
            $output->writeln("Fixed project: ".$project);
        }

        //presets ordering
        $presets_repository = $entity_manager->getRepository('AppTaskBundle:TaskPreset');
        $presets_query_builder = $presets_repository->getDefaultQueryBuilder();
        $presets_query_builder->join($presets_repository->column('category'), 'Category')
            ->add('select', 'Category', true)
            ->orderBy($presets_repository->column('order'), 'ASC');

        $presets = $presets_query_builder->getQuery()->getResult();

        $this->orderTasks($presets, $entity_manager);
        $entity_manager->flush();
        $output->writeln("Fixed presets order");
    }

    protected function orderTasks($tasks, EntityManager $entity_manager)
    {
        $tasks_by_categories = [];

        foreach($tasks as $task){
            /** @var Task $task */
            $category = $task->getCategory();
            $category_id = $category->getId();
            if(!isset($tasks_by_categories[$category_id])){
                $tasks_by_categories[$category_id] = [];
            }

            $order = count($tasks_by_categories[$category_id])+1;
            $tasks_by_categories[$category->getId()][] = $task;

            if($task->getOrder() != $order){
                $task->setOrder($order);
                $entity_manager->persist($task);
            }
        }
    }

} 