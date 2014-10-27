<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 20.02.14
 * Time: 12:30
 */

namespace App\InvoiceBundle\Form\Subscriber;


use App\InvoiceBundle\Entity\InvoiceTask;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskTaxesSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entity_manager;

    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
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
            FormEvents::PRE_SET_DATA => 'addTaxes',
            FormEvents::PRE_SUBMIT => 'addTaxes'
        ];
    }

    public function addTaxes(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $task = $data instanceof InvoiceTask ?
            $data->getTask() :
            $this->entity_manager->getRepository('AppTaskBundle:Task')->getById($data['task']);

        if($task !== null){
            //data for existing invoice item
            $taxes = $task->getTaxes();
        } else {
            //data for prototype
            $taxes = new ArrayCollection($this->entity_manager->getRepository('AppTaxBundle:Taxation')->getAll());
        }


        $form->add('taxes', 'taxes', array(
            'class' => 'AppTaxBundle:Taxation',
            'choices' => $taxes,
            'property' => 'taxTypeString',
            'required' => false,
            'label'=>false,
            'property_path' => 'task.taxes',
            'attr' => [
                'class' => 'taxes'
            ]
        ));
    }
} 