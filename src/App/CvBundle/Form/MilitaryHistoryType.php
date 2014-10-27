<?php
/**
 * MilitaryHistoryType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 17:20
 */

namespace App\CvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MilitaryHistoryType extends  AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'country', array('required' => false,'attr' => array('class' => 'form-control')))
            ->add('division', null, array('required' => false))
            ->add('unit', null, array('required' => false))
            ->add('rank', null, array('required' => false))
            ->add('campaign', null, array('label' => 'Campaign', 'required' => false))
            ->add('expertise', null, array('label' => 'Expertise', 'required' => false))
            ->add('recognition', null, array('required' => false))
            ->add('disciplinaryAction', null, array('label' => 'Disciplinary Action', 'required' => false))
            ->add('dischargeStatus', null, array('label' => 'Discharge Status', 'required' => false))
            ->add('serviceStatus', null, array('label' => 'Service Status', 'required' => false))
            ->add('description', 'tinymce', array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CvBundle\Entity\MilitaryHistory'
        ));
    }

    public function getName()
    {
        return 'military_history_form';
    }
}