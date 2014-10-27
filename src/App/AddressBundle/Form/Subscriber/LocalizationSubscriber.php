<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 17.06.14
 * Time: 14:30
 */

namespace App\AddressBundle\Form\Subscriber;


use App\AddressBundle\Entity\Address;
use App\AddressBundle\Entity\ProvinceRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

class LocalizationSubscriber implements EventSubscriberInterface
{
    protected $entityManager, $translator, $translationDomain;
    protected $emptyValue = 'Choose an option';

    function __construct(EntityManager $entityManager, TranslatorInterface $translator, $translationDomain)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
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
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $emptyValue = $this->translator->trans($this->emptyValue, [], $this->translationDomain);

        $provinceOptions = array('choices' => [], 'empty_value' => $emptyValue, 'required'=>false, 'disabled' => true,'attr' => array('class' => 'form-control'));
        $regionOptions = array('choices' => [], 'empty_value' => $emptyValue, 'required'=>false, 'disabled' => true,'attr' => array('class' => 'form-control'));

        if (!empty($data)) {
            $country = $data->getCountry();
            $province = (empty($country) ? [] : $data->getProvince());
            $region = (empty($province) ? [] : $province->getRegions());

            $provinceOptions['choices'] = $province;
            $provinceOptions['disabled'] = false;
            $regionOptions['choices'] = $region;
            $regionOptions['disabled'] = false;
        }

        $form->add('province', null, $provinceOptions);
        $form->add('region', null, $regionOptions);
    }

    public function preSubmitData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $emptyValue = $this->translator->trans($this->emptyValue, [], $this->translationDomain);

        $province = !empty($data['country']) && $data['country'] !== $emptyValue ?  $this->entityManager->getRepository('AppAddressBundle:Province')->getProvincesByCountries($data['country'], false) : [];
        $region = ctype_digit($data['province']) ? $this->entityManager->getRepository('AppAddressBundle:Region')->getRegionsByProvince($data['province'], false) : [];

        $form->add('province', null, array('choices' => $province, 'empty_value' => $this->emptyValue, 'required'=>false));
        $form->add('region', null, array('choices' => $region, 'empty_value' => $this->emptyValue, 'required'=>false));

    }
} 