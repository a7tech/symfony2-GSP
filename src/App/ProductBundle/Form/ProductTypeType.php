<?php
/**
 * ProductTypeType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 15.07.13 14:58
 */

namespace App\ProductBundle\Form;

use App\ProductBundle\Entity\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('description', 'textarea', array(
            'attr' => array(
                'class' => 'tinymce',
                'data-theme' => 'simple' // simple, medium, advanced, bbcode
            )
        ));
        $builder->add('type', 'choice', array(
            'empty_value' => 'Choose an option',
            'attr' => array('class' => 'form-control'),
            'choices' => array(
                ProductType::TYPE_PHYSICAL => 'Physical/shippable',
                ProductType::TYPE_VIRTUAL => 'Virtual/downloadable',
            )
        ));
    }

    public function getName()
    {
        return 'app_productbundle_producttypetype';
    }
}