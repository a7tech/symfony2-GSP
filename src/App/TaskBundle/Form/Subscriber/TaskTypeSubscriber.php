<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 09.02.14
 * Time: 19:21
 */

namespace App\TaskBundle\Form\Subscriber;


use App\StatusBundle\Utils\StatusTranslator;
use App\TaskBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskTypeSubscriber implements EventSubscriberInterface
{
    protected $statusTranslator;

    /** @var  Task */
    protected $task;

    public function __construct(StatusTranslator $statusTranslator)
    {
        $this->statusTranslator = $statusTranslator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'setRequired'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $this->task = $data;

        $this->setRequired($event);
    }

    public function setRequired(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Task $task */
        $task = $event->getData();

        $is_locked = $this->task->isLocked();

        $type = $task instanceof Task ? $task->getType() : ($is_locked ? $this->task->getType() : $task['type']);
        $cost_type = $task instanceof Task ? $task->getCostType() : ($is_locked ? $this->task->getCostType() : $task['costType']);

        $payments_mapped = $type != Task::TYPE_FREE;
        $quantity_mapped = $payments_mapped && $cost_type != Task::COST_TYPE_FIXED;

        $cost_types = $this->statusTranslator->getStatusesNames(Task::COST_TYPES_GROUP_NAME);
        $statuses = $this->statusTranslator->getStatusesNames(Task::STATUS_GROUP_NAME);

        $project = $this->task->getProject();
        $profit = $this->task->getRealProfit();

        $form->add('costType', 'choice', [
            'label' => 'cost_type',
            'choices' => $cost_types,
            'attr' => [
                'class' => 'cost-type'
            ],
            'by_reference' => false,
            'mapped' => $payments_mapped,
            'disabled' => $is_locked
        ])
        ->add('unitPrice', 'number', [
            'label' => 'unit_price',
            'mapped' => $payments_mapped,
            'disabled' => $is_locked,
            'attr' => [
                'class' => 'unit-price'
            ]
        ])
        ->add('unitQuantity', 'number', [
            'label' => 'quantity',
            'mapped' => $quantity_mapped,
            'disabled' => $is_locked,
            'attr' => [
                'class' => 'items-quantity'
            ]
        ])
        ->add('profit', 'percent', [
            'label' => 'mark_up',
            'mapped' => $payments_mapped,
            'disabled' => $is_locked,
            'precision' => 4,
            'attr' => [
                'class' => 'mark-up-input'
            ]
        ])
        ->add('realProfit', 'percent', [
            'label' => 'profit',
            'mapped' => false,
            'disabled' => $is_locked,
            'precision' => 4,
            'data' => $profit != 0 ? $profit : null,
            'attr' => [
                'class' => 'profit-input'
            ]
        ])
        ->add('estimatedTime', 'interval', array(
            'label' => 'estimated_time',
            'required' => false,
            'working_hours' => $project !== null ? $project->getWorkingHours() : 8
        ))
        ->add('status', 'choice', array(
            'label' => 'status',
            'choices' => $statuses,
            'required' => true,
            'attr' => [
                'class' => 'task-status'
            ]
        ));
    }

} 