<?php
/**
 * SalaryType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 23.08.13 17:21
 */

namespace App\HrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SalaryTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'tinymce', array('required' => false))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\HrBundle\Entity\SalaryType'
        ));
    }

    public function getName()
    {
        return 'salary_form';
    }
}