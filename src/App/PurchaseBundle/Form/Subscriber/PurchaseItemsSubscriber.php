<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 23.01.14
 * Time: 21:21
 */

namespace App\PurchaseBundle\Form\Subscriber;


use App\PurchaseBundle\Form\PurchaseWrapper\Item;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PurchaseItemsSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'submit'
        ];
    }

    public function submit(FormEvent $event)
    {
        $form_items = $event->getForm()->get('items');
        $product_supplier_price = [];

        foreach($form_items->all() as $item_form){
            /** @var Item $item */
            $item = $item_form->getData();

            if($item->getSupplier() !== null){
                if(!isset($product_supplier_price[$item->getSupplier()->getId()])){
                    $product_supplier_price[$item->getSupplier()->getId()] = [];
                }

                if(isset($product_supplier_price[$item->getSupplier()->getId()][$item->getAccountProduct()->getId()])){
                    if($product_supplier_price[$item->getSupplier()->getId()][$item->getAccountProduct()->getId()] != $item->getPrice()){
                        $item_form->get('price')->addError(new FormError('Prices of the same product and supplier cannot be different'));
                    }
                } else {
                    $product_supplier_price[$item->getSupplier()->getId()][$item->getAccountProduct()->getId()] = $item->getPrice();
                }
            }
        }

    }
} 