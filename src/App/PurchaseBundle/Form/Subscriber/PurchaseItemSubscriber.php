<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 23.01.14
 * Time: 22:39
 */

namespace App\PurchaseBundle\Form\Subscriber;


use App\AccountProductBundle\Entity\AccountProduct;
use App\PurchaseBundle\Form\PurchaseWrapper\Item;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class PurchaseItemSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;


    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Item $item */
        $item = $event->getData();

        $suppliers = $item !== null ? $item->getAccountProduct()->getSuppliers() : [];

        $this->addSupplierField($form, $suppliers);
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Item $item */
        $item = $event->getData();

        $accountProductId = $item['accountProduct'];
        /** @var AccountProduct $accountProduct */
        $accountProduct = $this->entityManager->getRepository('AppAccountProductBundle:AccountProduct')->getById($accountProductId);
        $suppliers = $accountProduct !== null ? $accountProduct->getSuppliers() : [];

        $this->addSupplierField($form, $suppliers);
    }

    protected function addSupplierField(FormInterface $form, $suppliers)
    {
        $form->add('supplier', 'entity', [
            'label' => false,
            'class' => 'App\\CompanyBundle\\Entity\\Company',
            'choices' => $suppliers,
            'attr' => [
                'class' => 'suppliers input-medium'
            ]
        ]);
    }
} 