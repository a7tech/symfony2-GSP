<?php
/**
 * BrandGroupType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 02.08.13 16:58
 */

namespace App\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'Name'))
            ->add('description', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'simple' // simple, medium, advanced, bbcode
                )
            ))
            ->add('purchaseCoeff', 'number', array('label' => 'Coeff 1 (Purchase)', 'required' => false))
            ->add('resellCoeff', 'number', array('label' => 'Coeff 2 (Resell)', 'required' => false))
        ;
    }

    public function getName()
    {
        return 'app_productbundle_brandgrouptype';
    }
}