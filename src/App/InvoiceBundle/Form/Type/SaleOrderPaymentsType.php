<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 05.02.14
 * Time: 17:27
 */

namespace App\InvoiceBundle\Form\Type;


use App\PurchaseBundle\Form\Type\Purchase\SupplierPaymentsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SaleOrderPaymentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('payments', new SupplierPaymentsType(), [
            'label' => false,
            'by_reference' => false,
            'attr' => [
                'class' => 'payments-form'
            ]
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\\InvoiceBundle\\Entity\\SaleOrder'
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_sale_order_payments';
    }

} 