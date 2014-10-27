<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 29.01.14
 * Time: 00:57
 */

namespace App\PurchaseBundle\Form\Type\Purchase;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupplierPaymentsType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'type' => 'payment',
            'allow_add'      => true,
            'allow_delete'   => true,
            'label_attr' => [
                'class' => 'supplier-label'
            ]
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_purchase_supplier_payments';
    }

    public function getParent()
    {
        return 'collection';
    }


} 