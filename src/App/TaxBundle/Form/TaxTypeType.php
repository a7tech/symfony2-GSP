<?php

namespace App\TaxBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaxTypeType extends AbstractType
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dql1 = "SELECT c from AppAddressBundle:Country c
                    where c.name='Canada' or c.name='United States' ";
        $results1 = $this->em->createQuery($dql1)->getResult();

        $preferedIsoChoices = array();

        foreach($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }

        $builder
            ->add('name')
            ->add('rate', 'percent', array('precision'=>true))
            ->add('description')
            ->add('country', 'entity', array(
                'class' => 'App\AddressBundle\Entity\Country',
                'label'=>'Country Code',
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'preferred_choices'=> $preferedIsoChoices,
                'required'=>true
            ))
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event) {
                    $form = $event->getForm();

                    // this would be your entity, i.e. SportMeetup
                    $data = $event->getData();
                    $country = $data->getCountry();

                    if (empty($country)) {
                        $form->add('province', null, array('empty_value' => 'Choose an option', 'required'=>false, 'attr'=>array('class'=>'form-control')));
                    }
                    else {
                        $positions = $country->getProvinces();
                        $form->add('province', null, array('choices' => $positions, 'empty_value' => 'Choose an option', 'attr'=>array('class'=>'form-control')));
                    }

                }
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event) {
                    $form = $event->getForm();

                    // this would be your entity, i.e. SportMeetup
                    $data = $event->getData();
                    $province = $data->getProvince();
                    if (empty($province)) {
                        $form->add('region', null, array('empty_value' => 'Choose an option', 'required'=>false, 'attr'=>array('class'=>'form-control')));
                    }
                    else {

                        if (empty($province)) {
                            $form->add('region', null, array('empty_value' => 'Choose an option', 'required'=>false, 'attr'=>array('class'=>'form-control')));
                        }
                        else {
                            $positions = $province->getRegions();
                            $form->add('region', null, array('choices' => $positions, 'empty_value' => 'Choose an option', 'attr'=>array('class'=>'form-control')));
                        }
                    }

                }
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TaxBundle\Entity\TaxType'
        ));
    }

    public function getName()
    {
        return 'app_taxbundle_taxtypetype';
    }
}
