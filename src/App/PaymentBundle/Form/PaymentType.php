<?php

namespace App\PaymentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'number', [
                'label' => 'amount',
                'attr' => [
                    'class' => 'paid-amount'
                ]
            ])
            ->add('payment_method', 'entity', [
                'label' => 'payment_method',
                'empty_value'=> 'Choose an option',
                'class' => 'App\PaymentBundle\Entity\PaymentMethod',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('a')->orderBy('a.name');
                }
            ])
            ->add('maturity', 'datepicker', [
                'label' => 'payment_deadline'
            ])
            ->add('payment_date', 'datepicker', [
                'label' => 'Payment date',
                'required' => false,
                'attr' => [
                    'class' => 'payment-date'
                ]
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PaymentBundle\Entity\Payment',
            'translation_domain' => 'Payment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'payment';
    }
}
