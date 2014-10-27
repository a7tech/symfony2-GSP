<?php

namespace App\AccountProductBundle\Form;

use App\AccountProductBundle\Form\Subscriber\TaxesSubscriber;
use App\ProductBundle\Form\FormMapper;
use App\ProductBundle\Form\PriceType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountProductType extends AbstractType
{

    public $em;
    public $profileId;

    public function __construct(EntityManager $em, $profileId) {

        $this->em = $em;
        $this->profileId = $profileId;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mapper = new FormMapper($builder);
        $mapper
            ->with('Product')
                ->add('product', null, array('empty_value'=>'Choose an option', 'attr' => array('class' => 'form-control')))
            ->with('Prices')
                ->add('price', new PriceType($this->em, $this->profileId), array('required' => false, 'embed_form' => true))
            ->with('Taxes')
                ->add('is_taxable', 'choice', array(
                    'label' => 'Taxable',
                    'choices' => array(1 => 'Taxable', 0 => 'Not taxable good'),
                    'expanded' => true,
                    'attr' => array('class' => 'checkbox')
                ))
            ->with('Suppliers')
                ->add('canBeOrderedFromSupplier', 'checkbox', array('label' => 'Can be ordered from supplier', 'required' => false))
                ->add('suppliers', 'company_autocomplete', array('required' => false))
            ->with('Stocks')
                ->add('canBeStocked', 'checkbox', array('label' => 'Can be stocked', 'required' => false))
                ->add('enQtyInc', 'choice', array('label' => 'Qty increment','attr' => array('class' => 'enQtyInc horizontal-list'), 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => false, 'expanded' => true, 'required' => false))
                ->add('sellQtyUnit', 'number', array('label' => 'Sell Qty Unit', 'required' => false))
                ->add('canBeDelivered', 'checkbox', array('label' => 'Can be delivered', 'required' => false))
                ->add('minQtyInStock', 'number', array('label' => 'Min Qty In Stock', 'required' => false))
                ->add('restockingQty', 'number', array('label' => 'Restocking Qty', 'required' => false))
                ->add('stockAvailability', 'choice', array('label' => 'Stock Availability', 'attr' => array('class' => 'shortSelect'), 'choices' => array(1 => 'In stock', 0 => 'Out of stock/discontinued'), 'empty_value' => false, 'expanded' => true, 'required' => false))
                ->add('sellOutOfStock', 'choice', array('label' => 'Sell Out Of Stock', 'attr' => array('class' => 'horizontal-list'), 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => false, 'expanded' => true, 'required' => false))
            ->with('Product exclusive for this buyers')
                ->add('buyers', 'company_autocomplete', array('required' => false))
        ;

        $builder->addEventSubscriber(new TaxesSubscriber($this->em));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AccountProductBundle\Entity\AccountProduct'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_accountproductbundle_accountproduct';
    }
}
