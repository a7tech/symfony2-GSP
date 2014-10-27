<?php
/**
 * CertificationType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 13:25
 */

namespace App\CvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CertificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('required' => true))
            ->add('firstDate', 'date', array(
                'required' => true,
                'label' => 'First Issued Date',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'datepicker')
            ))
            ->add('organization', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Organization',
                'required' => false
            ))
            ->add('number', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Number/ID',
                'required' => false
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false
            ))
            ->add('fromDate', 'date', array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Start Date',
                'attr' => array('class' => 'datepicker')
            ))
            ->add('toDate', 'date', array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'End Date',
                'attr' => array('class' => 'datepicker')
            ))
            ->add('current', 'checkbox', array(
                'label' => 'My current position',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CvBundle\Entity\Certification'
        ));
    }

    public function getName()
    {
        return 'certification_form';
    }
}