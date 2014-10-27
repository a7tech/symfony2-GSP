<?php
/**
 * ProductType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 10.07.13 0:35
 */

namespace App\ProductBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    public $em;

    public function __construct(EntityManager $em) {

        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mapper = new FormMapper($builder);
        $mapper
            ->with('General')
                ->add('productType', 'product_type', array(
                    'label' => 'Product Type',
                    'class' => 'AppProductBundle:ProductType',
                    'property' => 'formField',
                    'attr'=>['class' => 'form-control'],
                    'empty_value' => 'Choose a type',
                    'required' => true
                ))
                ->add('title', null, array('label' => 'Name'))
                ->add('description', 'tinymce')
                ->add('metaTitle', null, array('required' => false))
                ->add('metaKeywords', null, array('required' => false))
                ->add('metaDescription', null, array('required' => false))
                ->add('slug')
            ->with('Prices')
                ->add('price', new PriceType($this->em), array('required' => false, 'embed_form' => true))
//            ->with('Taxes')
//                ->add('is_taxable', 'choice', array(
//                    'label' => 'Taxable',
//                    'choices' => array(1 => 'Taxable', 0 => 'Not taxable good'),
//                    'expanded' => true,
//                ))
//                ->add('taxes', 'entity_sorted', array(
//                    'class' => 'AppTaxBundle:TaxType',
//                    'expanded' => true,
//                    'required' => false,
//                    'multiple' => true
//                ))
            ->with('Codes / Barcode')
                ->add('productCode', 'text', array('label' => 'Product Code', 'required' => true))
                ->add('barcodes', 'collection', array(
                    'type' => 'barcode_form',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'horizontal-form'),
                    'required' => false
                ))
            ->with('Weight & Size')
                ->add('width', 'number', array('required' => false))
                ->add('height', 'number', array('required' => false))
                ->add('depth', 'number', array('required' => false))
                ->add('weight', 'number', array('required' => false))
            ->with('Groups')
                ->add('categories', 'category_tree', array(
                    'class' => 'AppProductBundle:Category',
                    'attr' => array('class' => 'product-categories checkbox'),
                    'required' => false,
                    'label' =>'Product Categories'
                ))
                ->add('brandGroup', 'entity_sorted', array(
                    'label' => 'Brand Group',
                    'empty_value' => 'Choose a brand group',
                    'class' => 'AppProductBundle:BrandGroup',
                    'attr' => ['class' => 'form-control'],
                    'required' => false
                ))
            ->with('Image / Media')
                ->add('videoLink', null, array('label' => 'Video Link', 'required' => false))
                ->add('images', 'collection', array(
                    'type' => new ProductImageType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'attr' => array('class' => 'collection image-upload'),
                    'required' => false
                ))
//            ->with('Suppliers')
//                ->add('canBeOrderedFromSupplier', 'checkbox', array('label' => 'Can be ordered from supplier', 'required' => false))
//                ->add('suppliers', 'company_autocomplete', array('required' => false))
//            ->with('Stocks')
//                ->add('canBeStocked', 'checkbox', array('label' => 'Can be stocked', 'required' => false))
//                ->add('enQtyInc', 'choice', array('label' => 'Qty increment','attr' => array('class' => 'enQtyInc horizontal-list'), 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => false, 'expanded' => true, 'required' => false))
//                ->add('sellQtyUnit', 'number', array('label' => 'Sell Qty Unit', 'required' => false))
//                ->add('canBeDelivered', 'checkbox', array('label' => 'Can be delivered', 'required' => false))
//                ->add('minQtyInStock', 'number', array('label' => 'Min Qty In Stock', 'required' => false))
//                ->add('restockingQty', 'number', array('label' => 'Restocking Qty', 'required' => false))
//                ->add('stockAvailability', 'choice', array('label' => 'Stock Availability', 'attr' => array('class' => 'shortSelect'), 'choices' => array(1 => 'In stock', 0 => 'Out of stock/discontinued'), 'empty_value' => false, 'expanded' => true, 'required' => false))
//                ->add('sellOutOfStock', 'choice', array('label' => 'Sell Out Of Stock', 'attr' => array('class' => 'horizontal-list'), 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => false, 'expanded' => true, 'required' => false))
//            ->with('Product exclusive for this buyers')
//                ->add('buyers', 'company_autocomplete', array('required' => false))
            ->with('Visibility')
                ->add('livetime', 'number', array('required' => false))
                ->add('liveTimeAmount', 'choice', array('choices' => array(0 => 'Days', 1 => 'Weeks', 2=> 'Months', 3=>'Years'), 'empty_value' => 'Choose an option', 'required' => false, 'attr' => array('class' => 'form-control')))
                ->add('productionTime', 'number', array('label' => 'Production Time', 'required' => false))
                ->add('productionTimeAmount', 'choice', array('choices' => array(0 => 'Days', 1 => 'Weeks', 2=> 'Months', 3=>'Years'), 'empty_value' => 'Choose an option', 'required' => false, 'attr' => array('class' => 'form-control')))
                ->add('newFromDate', 'datepicker', array('label' => 'New Form Date', 'required' => false))
                ->add('newToDate', 'datepicker', array('label' => 'New To Date', 'required' => false))
                ->add('manufactureCountry', 'country', array('label' => 'Manufacture Country', 'attr' => array('class' => 'shortSelect'), 'required' => false, 'attr' => array('class' => 'form-control')))
                ->add('showOnFront', 'choice', array('attr' => array('label' => 'Show On Front', 'class' => 'horizontal-list radio'), 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => false, 'expanded' => true, 'required' => false))
                ->add('onlinePurchaseable', 'choice', array('label' => 'Online Purchaseable', 'attr' => array('class' => 'horizontal-list radio'), 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => false, 'expanded' => true, 'required' => false))
                ->add('isActive', 'checkbox', array('label' => 'Is active', 'required' => false))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProductBundle\Entity\Product'
        ));
    }

    public function getName()
    {
        return 'product_form';
    }
}