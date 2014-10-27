<?php
/**
 * BarcodeType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 05.08.13 2:29
 */

namespace App\BarcodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BarcodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('barcodeType', null,
                  array(
                    'label' => 'Barcode Type',
                    'empty_value' => 'Choose an option',
                    'required'=>true,
                    'attr' => array('class' => 'form-control'),  
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                        }
                    ))
            ->add('number', null, array('label' => 'Barcode Number'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\BarcodeBundle\Entity\Barcode'
        ));
    }

    public function getName()
    {
        return 'barcode_form';
    }
}