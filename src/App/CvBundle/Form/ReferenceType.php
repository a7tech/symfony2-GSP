<?php
/**
 * ReferenceType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 17:29
 */

namespace App\CvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, array('label' => 'First Name', 'required' => true))
            ->add('lastName', null, array('label' => 'Last Name', 'required' => true))
            ->add('middleName', null, array('label' => 'Middle Name', 'required' => false))
            ->add('title', null, array('label' => 'Title', 'required' => false))
            ->add('email', null, array('label' => 'Email', 'required' => true))
            ->add('phone', null, array('label' => 'Phone', 'required' => false))
            ->add('organization', null, array('label' => 'Organization', 'required' => false))
            ->add('relationship', null, array('label' => 'Relationship', 'required' => false))
            ->add('lengthTime', null, array('label'=>'Length Of Time (months)'))
            ->add('description', 'tinymce', array('label' => 'Comment', 'required' => false))
            ->add('canContact', 'checkbox', array('label' => 'Can Contact?', 'required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CvBundle\Entity\Reference'
        ));
    }

    public function getName()
    {
        return 'reference_form';
    }
}