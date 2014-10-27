<?php
/**
 * BarcodeType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 05.08.13 1:51
 */

namespace App\BarcodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BarcodeTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\BarcodeBundle\Entity\BarcodeType'
        ));
    }

    public function getName()
    {
        return 'app_barcodebundle_barcodetypetype';
    }
}