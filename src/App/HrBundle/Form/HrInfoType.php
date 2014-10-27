<?php
/**
 * HrInfoType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 23.08.13 15:49
 */

namespace App\HrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HrInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('receiveCommissions', 'checkbox', array('label' => 'Receive commissions', 'required' => false))
            ->add('nationality', 'text', array('required' => false))
            ->add('idNumber', 'text', array('label' => 'Id No', 'required' => false))
            ->add('socialSecurityNumber', 'text', array('label' => 'Social Security No', 'required' => false))
            ->add('drivingLicence', 'text', array('label' => 'Driving licence', 'required' => false))
            ->add('otherId', 'text', array('label' => 'Other Id', 'required' => false))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\HrBundle\Entity\HrInfo'
        ));
    }

    public function getName()
    {
        return 'hr_info_form';
    }
}