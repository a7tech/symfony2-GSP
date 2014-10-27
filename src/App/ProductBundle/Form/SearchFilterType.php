<?php
/**
 * ProductType
 *
 * @author Ricardo Renteria
 */

namespace App\ProductBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, array('label' => 'Name', 'required' => false))
        ->add('categories', 'select_category', array(
                'label' =>'Product Category',
                'class' => 'AppProductBundle:Category',
                'property' => 'formField',
                'empty_value' => 'Choose a type',
                'attr'=> ['class' => 'form-control'],
                'required' => false,
                
        ))
        ->add('productCode', 'text', array('label' => 'Product Code', 'required' => false))
        ->add('upcCode', null, array('label' => 'UPC code', 'required' => false))
        ->add('companyGroups', 'entity', array(
                    'label' => 'Provider',
                    'class'=>'AppCompanyBundle:CompanyGroup',
                    'attr'=> ['class' => 'form-control'],
                    'expanded'=>false,
                    'multiple'=>false,
                    'required' => false,
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('cg')->orderBy('cg.title', 'ASC');
                        return $qb;}))

        ->add('brandGroup', 'entity_sorted', array(
                'label' => 'Brand',
                'empty_value' => 'Choose a brand group',
                'class' => 'AppProductBundle:BrandGroup',
                'required' => false,
                'expanded'=>false,
                'multiple'=>false,
                'attr'=> ['class' => 'form-control'],))
        ->add('createdAtFrom', 'datepicker', array('label' => 'Creation Date From', 'required' => false))
        ->add('createdAtTo', 'datepicker', array('label' => 'Creation Date To', 'required' => false))
        ->add('isActive', 'choice', array('attr' => array('class' => 'form-control'), 'label' => 'Actif', 'choices' => array(1 => 'Yes', 0 => 'No'), 'empty_value' => 'yes or no', 'expanded' => false, 'required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'csrf_protection'   => false,
                'validation_groups' => array('filtering')
        ));
    }
    
    public function getName()
    {
        return 'search';
    }
   
}