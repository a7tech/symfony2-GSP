<?php

namespace App\AddressBundle\Form;

use App\AddressBundle\Entity\Country;
use App\AddressBundle\Entity\Province;
use App\AddressBundle\Form\Subscriber\LocalizationSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Translation\TranslatorInterface;

class AddressType extends AbstractType
{
    protected $em;
    protected $translator;

    public function __construct(EntityManager $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $factory = $builder->getFormFactory();

        $dql1 = "SELECT c from AppAddressBundle:Country c
                    where c.name='Canada' or c.name='United States' ";
        $results1 = $this->em->createQuery($dql1)->getResult();

        $preferedIsoChoices = array();

        foreach($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }

        $builder
            ->add('isMain', null, array('label'=>'Main address', 'required'=>false, 'attr' => ['class' => 'is-main-address']))
            ->add('isBilling', null, array('label'=>'Billing address', 'required'=>false))
            ->add('isShipping', null, array('label'=>'Shipping address', 'required'=>false))
            ->add('addressType', null,
                  array(
                        'label'=>'Address Type',
                        'empty_value' => 'Choose an option',
                        'required'=>true,
                        'attr' => array('class' => 'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }))
            ->add('contact', null, array('label'=>'Contact person', 'required'=>false))
            ->add('building', null, array('label'=>'Building №','attr'=>array('class'=>'input-small')))
            ->add('street')
            ->add('suite', null, array('attr'=>array('class'=>'input-small')))
            ->add('po', null, array('attr'=>array('lable'=>'P.O. BOX')))
            ->add('city')
            ->add('postcode', null, array('label'=>'ZIP Code', 'attr'=>array('class'=>'input-small')))
            ->add('country', 'entity', array(
                'class' => 'App\AddressBundle\Entity\Country',
                'label'=>'Country Code',
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'preferred_choices'=> $preferedIsoChoices,
                'required'=>true
            ))
            ->addEventSubscriber(new LocalizationSubscriber($this->em, $this->translator, $options['translation_domain']))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AddressBundle\Entity\Address',
            'attr' => [
                'class' => 'address'
            ]
        ));
    }

    public function getName()
    {
        return 'address_form';
    }
}
