<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 11.02.14
 * Time: 14:07
 */

namespace App\PurchaseBundle\Form\Subscriber;


use App\AccountProductBundle\Entity\AccountProduct;
use App\InvoiceBundle\Entity\InvoiceProduct;
use App\PurchaseBundle\Form\PurchaseWrapper\Item;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PurchaseProductTaxesSubscriber implements EventSubscriberInterface
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

        $account_product = $data instanceof Item ?
            $data->getAccountProduct() :
            $this->entity_manager->getRepository('AppAccountProductBundle:AccountProduct')->getById($data['accountProduct']);

        if($account_product !== null){
            //data for existing invoice item
            $taxes = $account_product->getTaxes();
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
            'attr' => [
                'class' => 'taxes'
            ]
        ));
    }
} 