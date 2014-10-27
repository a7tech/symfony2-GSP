<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 20.02.14
 * Time: 12:21
 */

namespace App\InvoiceBundle\Form\Subscriber;


use App\InvoiceBundle\Entity\SaleOrder;
use App\InvoiceBundle\Form\Type\TaskCreditType;
use App\InvoiceBundle\Form\Type\TasksType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProjectInvoiceSubscriber implements EventSubscriberInterface
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
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
        $data = $event->getData();

        if($data instanceof SaleOrder){
            $project = $data->getProject();
            if($project !== null){
                if($data->isDepositInvoice()){
                    $taxes = $data->getVendorCompany()->getTaxation();
                    $total = $data->getProject()->getContractNetCost($this->entityManager, false);

                    $form->add('depositPosition', 'number', [
                        'label' => false,
                        'attr' => [
                            'class' => 'deposit-amount price',
                            'data-project-total' => $total
                        ]
                    ])->add('percent', 'percent', [
                        'label' => false,
                        'precision' => 4,
                        'mapped' => false,
                        'data' => ($data->getDepositPosition() / $total),
                        'attr' => [
                            'class' => 'deposit-percent'
                        ]
                    ])->add('depositTaxes', 'taxes', [
                        'class' => 'AppTaxBundle:Taxation',
                        'choices' => $taxes,
                        'property' => 'taxTypeString',
                        'required' => false,
                        'label'=>false,
                        'attr' => [
                            'class' => 'taxes'
                        ]
                    ]);
                } elseif(!$data->isCredit()) {
                    $allow_add = $data->getProjectCategory() === null;

                    //tasks for project invoice
                    $form->add('tasks', new TasksType($this->entityManager), [
                        'type'         => 'invoice_task',
                        'label'        => false,
                        'allow_add'    => $allow_add,
                        'allow_delete' => $allow_add,
                        'show_add'     => false,
                        'by_reference' => false,
                        'attr'         => [
                            'class' => 'tasks-items'
                        ]
                    ]);

                    if ($allow_add) {
                        //adjustment invoice
                        $form->add('not_invoiced_tasks', 'invoice_not_invoiced_tasks', [
                            'label' => 'not_invoiced_tasks',
                            'mapped'   => false,
                            'project'  => $project,
                            'required' => false
                        ]);
                    }
                } else {
                    //project credit invoice
                    $form->add('not_credited_tasks', 'invoice_not_credited_tasks', [
                        'label' => 'not_credited_tasks',
                        'mapped' => false,
                        'project' => $project,
                        'required' => false
                    ])->add('credit_tasks', 'collection', [
                        'type' => new TaskCreditType($this->entityManager),
                        'label' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'show_add' => false,
                        'by_reference' => false,
                        'property_path' => 'tasks',
                        'attr' => [
                            'class' => 'tasks-credit-items'
                        ]
                    ]);
                }
            }
        }
    }


} 