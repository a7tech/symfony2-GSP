<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.03.14
 * Time: 01:32
 */

namespace App\TaskBundle\Manager;


use App\TaskBundle\Entity\TaskBase;
use Doctrine\ORM\EntityManager;

abstract class TaskBaseManager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function normalizeItemsOrdering($task, $tasks, $update = true)
    {
        /** @var TaskBase $task */
        if($task->getId() === null && $task->getOrder() === null){
            //add
            $task->setOrder(count($tasks)+1);
        } elseif($update === true){
            //update
            //take out task
            foreach($tasks as $key => $sibling){
                /** @var TaskBase $sibling */
                if($sibling->getId() == $task->getId()){
                    unset($tasks[$key]);
                    break;
                }
            }

            //normalize order
            $order = $task->getOrder();
            $count = count($tasks);
            if($order > $count){
                $tasks[] = $task;
            } else {
                array_splice($tasks, $order-1, 0, [$task]);
            }

            $i = 1;
            foreach($tasks as $sibling){
                /** @var TaskBase $sibling */
                if($sibling->getOrder() != $i){
                    $sibling->setOrder($i);
                    if($sibling->getId() !== $task->getId()) {
                        $this->entityManager->persist($sibling);
                    }
                }

                $i++;
            }
        } else {
            //delete
            $i = 1;
            foreach($tasks as $sibling){
                /** @var TaskBase $sibling */
                if($sibling->getId() != $task->getId()){
                    if($sibling->getOrder() != $i){
                        $sibling->setOrder($i);
                        $this->entityManager->persist($sibling);
                    }
                    $i++;
                }
            }
        }
    }

}