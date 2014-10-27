<?php
/**
 * AssociationType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 13:42
 */

namespace App\CvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Name', 'required' => true
            ))
            ->add('link', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Link', 'required' => false
            ))
            ->add('role', null, array(
                'attr' => array('class' => 'shortSelect'),
                'label' => 'Role', 'required' => true
            ))
            ->add('description', 'textarea', array(
                'label' => 'Comments',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false
            ))
            ->add('startDate', 'date', array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Start Date',
                'attr' => array('class' => 'datepicker')
            ))
            ->add('endDate', 'date', array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'End Date',
                'attr' => array('class' => 'datepicker')
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CvBundle\Entity\Association'
        ));
    }

    public function getName()
    {
        return 'association_form';
    }
}