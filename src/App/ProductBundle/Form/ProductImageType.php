<?php
/**
 * ProductImageType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 08.08.13 16:37
 */

namespace App\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', 'vlabs_file')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProductBundle\Entity\ProductImage'
        ));
    }

    public function getName()
    {
        return 'app_productbundle_productimagetype';
    }
}