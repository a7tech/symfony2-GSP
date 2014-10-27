<?php

namespace App\InvoiceBundle\Form;

use App\CompanyBundle\Entity\CommonCompany;
use App\InvoiceBundle\Form\Subscriber\AddressesSubscriber;
use App\InvoiceBundle\Form\Subscriber\ProjectInvoiceSubscriber;
use App\PersonBundle\Entity\Person;
use App\ProductBundle\Form\FormMapper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SaleOrderType extends AbstractType
{

    public  $em;
    public  $customer;
    public  $customerCompany;

    public function __construct(EntityManager $em, Person $customer=null, CommonCompany $customerCompany=null)
    {
        $this->em = $em;
        $this->customer = $customer;
        $this->customerCompany = $customerCompany;
    }

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customer = $this->customer;
        $customerCompany = $this->customerCompany;

        $builder
            ->add('customerCompany', 'company_single_not_required_autocomplete')
            ->add('customer', 'person_notrequired_autocomplete', [
                'label' => 'customer',
                'translation_domain' => 'Invoice',
            ])
//            ->with('Vendor info')
            ->add('vendorCompany', null, array(
                'required'=>true,
                'label' => 'vendor_company',
                'attr' => array('class' => 'form-control')
            ))
            ->add('invoiceDate', 'datepicker', [
                'label' => 'invoice_date'
            ])
            ->add('comment', null, [
                'label' => 'comment'
            ])
//            ->with('Invoice info')

            ->add('isRbq', null, array('label'=>'add_rbq_number'))
            ->add('isTaxable', null, array('label'=>'add_taxes'))
            ->add('showDeliveryAddress', null, [
                'label' => 'show_delivery_address'
            ])
//            ->with('Projects')
//            ->add('project', null, array('empty_value'=>'Choose an option'))
//            ->with('Products')
            ->add('products', 'collection', [
                'type' => new \App\InvoiceBundle\Form\InvoiceProductType($this->em),
                'allow_add' => true,
                'allow_delete' => true,
                'show_add' => false,
                'label' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'purchase-items'
                ]
            ]);


        $builder->addEventSubscriber(new ProjectInvoiceSubscriber($this->em))
                 ->addEventSubscriber(new AddressesSubscriber($this->em));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\InvoiceBundle\Entity\SaleOrder',
            'translation_domain' => 'Invoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'invoice';
    }
}
