<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-14
 * Time: 20:31
 */

namespace App\InvoiceBundle\Form\Type\Returns;


use App\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use App\InvoiceBundle\Entity\Returns\ReturnType;
use App\InvoiceBundle\Form\Subscriber\ProductReturnSubscriber;
use App\InvoiceBundle\Form\Transformer\ProductsReturnsTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductReturnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('quantity', 'integer', [
            'label' => 'product_return.quantity',
            'attr' => [
                'class' => 'return-quantity input-mini',
            ]
        ])->add('returnType', 'choice', [
            'label' => 'return_type',
            'choices' => ReturnType::getTypesNames('return_type.'),
            'attr' => [
                'class' => 'input-small return-type'
            ]
        ])->add('returnReason', 'entity', [
            'label' => 'return_reason',
            'class' => 'App\InvoiceBundle\Entity\Returns\ProductReturnReason',
            'attr' => [
                'class' => 'input-small'
            ]
        ])->add('refundPrice', null, [
            'label' => 'refund_price',
            'precision' => 2,
            'attr' => [
                'class' => 'refund-price input-small'
            ]

        ])->add('description', 'textarea', [
            'label' => 'return_description',
            'required' => false,
            'attr' => [
                'style' => 'resize: vertical'
            ]
        ])->add('invoiceProduct', 'hidden', [
            'attr' => [
                'class' => 'invoice-product-id'
            ]
        ]);

        $builder->get('invoiceProduct')->addModelTransformer(new ProductsReturnsTransformer($options['invoice_products']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\InvoiceBundle\Entity\InvoiceProductReturn',
            'translation_domain' => 'Invoice',
            'invoice_products' => [],
            'error_bubbling' => false,
            'cascade_validation' => true
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_invoice_product_return';
    }

} 