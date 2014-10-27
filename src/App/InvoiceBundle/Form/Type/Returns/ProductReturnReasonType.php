<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-14
 * Time: 07:57
 */

namespace App\InvoiceBundle\Form\Type\Returns;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductReturnReasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, [
            'label' => 'product_return_reason.name',
        ])->add('isStockIncreased', 'checkbox', [
            'label' => 'product_return_reason.is_stock_increased',
            'required' => false
        ])->add('isStockBlocked', 'checkbox', [
            'label' => 'product_return_reason.is_stock_blocked',
            'required' => false
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\InvoiceBundle\Entity\Returns\ProductReturnReason',
            'translation_domain' => 'Invoice'
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'invoice_product_return_reason';
    }

} 