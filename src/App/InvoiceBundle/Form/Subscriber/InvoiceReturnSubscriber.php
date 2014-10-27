<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-17
 * Time: 13:12
 */

namespace App\InvoiceBundle\Form\Subscriber;

use App\InvoiceBundle\Entity\SaleOrder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class InvoiceReturnSubscriber implements EventSubscriberInterface
{
    protected $entityManager;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var SaleOrder $data */
        $data = $event->getData();

        if ($data->isDepositInvoice()) {
            $total = $data->getProject()->getContractNetCost($this->entityManager, false);

            $form->add('depositPosition', 'number', [
                'label' => 'deposit_amount',
                'attr' => [
                    'class' => 'deposit-amount',
                    'data-project-total' => $total
                ]
            ])->add('percent', 'percent', [
                'label' => 'deposit_amount_percentage',
                'precision' => 4,
                'mapped' => false,
                'data' => ($data->getDepositPosition() / $total),
                'attr' => [
                    'class' => 'deposit-percent'
                ]
            ]);
        } elseif ($data->getProject() !== null) {
            $form->add('tasks', 'collection', [
                'type' => 'backend_invoice_task_return',
                'allow_add' => true,
                'show_add' => false,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'task-returns-collection', // TO CHANGE
                ],
                'options' => [
                    'invoice_tasks' => $data->getTasks(),
                ]
            ]);
        } else {
            $form->add('productReturns', 'collection', [
                'type' => 'backend_invoice_product_return',
                'allow_add' => true,
                'show_add' => false,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'product-returns-collection'
                ],
                'options' => [
                    'invoice_products' => $data->getProducts()
                ]
            ]);
        }
    }
} 