<?php
/**
 * SalaryInfoType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 23.08.13 15:56
 */

namespace App\HrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SalaryInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currency', 'entity_sorted', array(
                'class' => 'App\CurrencyBundle\Entity\Currency',
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('salaryType', 'entity_sorted', array(
                'class' => 'App\HrBundle\Entity\SalaryType',
                'label' => 'Salary',
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('amount', 'number', array('required' => false))
            ->add('Monday', 'integer', array('required' => false))
            ->add('Tuesday', 'integer', array('required' => false))
            ->add('Wednesday', 'integer', array('required' => false))
            ->add('Thursday', 'integer', array('required' => false))
            ->add('Friday', 'integer', array('required' => false))
            ->add('Saturday', 'integer', array('required' => false))
            ->add('Sunday', 'integer', array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\HrBundle\Entity\SalaryInfo'
        ));
    }

    public function getName()
    {
        return 'salary_info_form';
    }
}