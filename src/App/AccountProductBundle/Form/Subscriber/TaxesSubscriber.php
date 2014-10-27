<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 11.02.14
 * Time: 13:00
 */

namespace App\AccountProductBundle\Form\Subscriber;


use App\AccountBundle\Entity\AccountProfile;
use App\AccountProductBundle\Entity\AccountProduct;
use App\CoreBundle\Utils\ObjectsUtils;
use App\TaxBundle\Entity\Taxation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaxesSubscriber implements EventSubscriberInterface
{
    protected $entity_manager;

    /**
     * @var AccountProfile
     */
    protected $account_profile;

    public  function __construct(EntityManager $entity_manager)
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
            FormEvents::PRE_SET_DATA => 'addTaxation',
            FormEvents::PRE_SUBMIT => 'addTaxation'
        ];
    }

    public function addTaxation(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        /** @var AccountProfile $account_profile */
        $account_profile = null;

        if($data instanceof AccountProduct){
            $account_profile = $data->getAccount();

            //save account profile for submit
            $this->account_profile = $account_profile;
        } else {
            $account_profile = $this->account_profile;
        }

        $taxation = $account_profile->getTaxation();

        $form->add('taxes', 'entity', [
            'required' => false,
            'class' => 'App\TaxBundle\Entity\Taxation',
            'property' => 'taxTypeString',
            'choices' => $taxation,
            'multiple' => true,
            'expanded' => true,
            'group' => 'Taxes'
        ]);
    }

} 