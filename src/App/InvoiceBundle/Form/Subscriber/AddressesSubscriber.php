<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-04-07
 * Time: 17:21
 */

namespace App\InvoiceBundle\Form\Subscriber;


use App\InvoiceBundle\Entity\SaleOrder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddressesSubscriber implements EventSubscriberInterface
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
            FormEvents::PRE_SET_DATA => 'addFields',
            FormEvents::PRE_SUBMIT => 'addFields'
        ];
    }

    public function addFields(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $customer = null;
        $customerCompany = null;

        if($data instanceof SaleOrder){
            $customer = $data->getCustomer();
            $customerCompany = $data->getCustomerCompany();
        } else {
            $customer = $this->entityManager->getRepository('AppPersonBundle:Person')->getById($data['customer']);
            $customerCompany = $this->entityManager->getRepository('AppCompanyBundle:CommonCompany')->getById($data['customerCompany']);
        }

        $form->add('billing','entity',
        array(
            'label' => 'billing_address',
            'translation_domain' => 'Address',
            'class'=>'App\AddressBundle\Entity\Address',
            'required' => true,
            'empty_value' => 'Choose an option',
            'query_builder'=> function(EntityRepository $repository) use ($customer, $customerCompany) {
                $qb = $repository->createQueryBuilder('b');
                if ($customerCompany) {
                    $qb = $repository->createQueryBuilder('b');
                    $qb = $qb->innerJoin('b.companies', 'p')->where('p.id = :id')
                        ->setParameter('id', $customerCompany->getId());

                    return $qb;
                } elseif ($customer) {
                    $qb = $repository->createQueryBuilder('b')
                        ->innerJoin('b.persons', 'p');
                    $qb = $qb->where('p.id = :id')
                        ->setParameter('id', $customer->getId());

                    return $qb;
                }
                return $qb;

            }
        ))
        ->add('shipment' ,'entity',
            array(
                'label' => 'shipping_address',
                'translation_domain' => 'Address',
                'class'=>'App\AddressBundle\Entity\Address',
                'required' => false,
                'empty_value' => 'Choose an option',
                'query_builder'=>function(EntityRepository $repository) use ($customer, $customerCompany) {
                    $qb = $repository->createQueryBuilder('b');
                    if ($customerCompany) {
                        $qb = $qb->innerJoin('b.companies', 'p')->where('p.id = :id')
                            ->setParameter('id', $customerCompany->getId());

                        return $qb;
                    } elseif ($customer) {
                        $qb->innerJoin('b.persons', 'p');
                        $qb = $qb->where('p = :person')
                                ->setParameter('person', $customer);

                        return $qb;
                    }

                    return $qb;

                }
            ));


    }

} 