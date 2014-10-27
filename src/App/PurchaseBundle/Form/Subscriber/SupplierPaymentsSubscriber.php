<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 29.01.14
 * Time: 01:01
 */

namespace App\PurchaseBundle\Form\Subscriber;


use App\CoreBundle\Utils\ObjectsUtils;
use App\PurchaseBundle\Form\Type\Purchase\SupplierPaymentsType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SupplierPaymentsSubscriber implements EventSubscriberInterface
{
    protected $suppliers;

    public function __construct($suppliers)
    {
        $this->suppliers = ObjectsUtils::extractIds($suppliers);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'addPayments',
            FormEvents::PRE_SUBMIT => 'addPayments'
        ];
    }

    public function addPayments(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if(is_array($data)){
            foreach(array_keys($data) as $supplier_id){
                $form->add($supplier_id, new SupplierPaymentsType(), [
                    'label' => $this->suppliers[$supplier_id]->getName(),
                ]);
            }
        }

    }

} 