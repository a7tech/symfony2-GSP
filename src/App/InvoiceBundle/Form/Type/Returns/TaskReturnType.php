<?php

namespace App\InvoiceBundle\Form\Type\Returns;

use App\InvoiceBundle\Form\Transformer\TaskReturnsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskReturnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('refund', null, [
            'label' => 'refund_price',
            'attr' => [
                'class' => 'refund-price input-small'
            ]
        ])
        ->add('refundDescription', 'textarea', [
            'label' => 'return_description',
            'required' => false,
            'attr' => [
                'style' => 'resize: vertical',
                'class' => 'refund-description',
            ]
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\InvoiceBundle\Entity\InvoiceTask',
            'translation_domain' => 'Invoice',
            'invoice_tasks' => [],
            'error_bubbling' => false,
            'cascade_validation' => true
        ]);
    }

    public function getName()
    {
        return 'backend_invoice_task_return';
    }
}