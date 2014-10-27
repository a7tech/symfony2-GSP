<?php
namespace App\TaskBundle\Form\Subscriber;


use App\StatusBundle\Utils\StatusTranslator;
use App\TaskBundle\Entity\TaskBase;
use App\TaskBundle\Entity\TaskPreset;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskPresetTypeSubscriber implements EventSubscriberInterface
{
    protected $statusTranslator;

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
            FormEvents::PRE_SET_DATA => 'setRequired',
            FormEvents::PRE_SUBMIT => 'setRequired'
        ];
    }

    public function setRequired(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Task $task */
        $task = $event->getData();

        $type = $task instanceof TaskPreset ? $task->getType() : $task['type'];
        $cost_type = $task instanceof TaskPreset ? $task->getCostType() : $task['costType'];

        $payments_mapped = $type != TaskPreset::TYPE_FREE;
        $quantity_mapped = $payments_mapped && $cost_type != TaskPreset::COST_TYPE_FIXED;

        $cost_types = $this->statusTranslator->getStatusesNames(TaskPreset::COST_TYPES_GROUP_NAME);
        $profit = ($task instanceof TaskBase) ? $task->getRealProfit() : 0;

        $form->add('costType', 'choice', [
            'label' => 'cost_type',
            'choices' => $cost_types,
            'attr' => [
                'class' => 'cost-type'
            ],
            'by_reference' => false,
            'mapped' => $payments_mapped
        ])
        ->add('unitPrice', 'number', [
            'label' => 'unit_price',
            'mapped' => $payments_mapped,
            'attr' => [
                'class' => 'unit-price'
            ]
        ])
        ->add('unitQuantity', 'number', [
            'label' => 'quantity',
            'mapped' => $quantity_mapped,
            'attr' => [
                'class' => 'items-quantity'
            ]
        ])
        ->add('profit', 'percent', [
            'label' => 'mark_up',
            'mapped' => $payments_mapped,
            'precision' => 4,
            'attr' => [
                'class' => 'mark-up-input'
            ]
        ])
        ->add('realProfit', 'percent', [
            'label' => 'profit',
            'mapped' => false,
            'precision' => 4,
            'data' => $profit != 0 ? $profit : null,
            'attr' => [
                'class' => 'profit-input'
            ]
        ]);
    }

} 