<?php

namespace App\PhoneBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhoneType extends AbstractType
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $dql1 = "SELECT p from AppPhoneBundle:PhoneIso p
                    Join p.country c
                    where c.name='Canada' or c.name='United States' ";
        $results1 = $this->em->createQuery($dql1)->getResult();

        $preferedIsoChoices = array();

        foreach($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }



        $builder
            ->add('phoneType', null,
                  array(
                        'label'=>'Phone Type',
                        'empty_value' => 'Choose an option',
                        'attr'=>array('class'=>'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }
                        ))
            ->add('phoneIsoCode', 'entity', array(
                            'class' => 'App\PhoneBundle\Entity\PhoneIso',
                            'label'=>'Country Code',
                            'empty_value' => 'Choose an option',
                            'attr'=>array('class'=>'form-control'),
                            'preferred_choices'=> $preferedIsoChoices
            ))
            ->add('number', null, array('attr'=>array('class'=>'input-medium')))
            ->add('extension', null, array('attr'=>array('class'=>'input-small')))


        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PhoneBundle\Entity\Phone'
        ));
    }

    public function getName()
    {
        return 'phone_form';
    }
}
