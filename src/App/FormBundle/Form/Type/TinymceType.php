<?php
/**
 * TinymceType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 23.08.13 17:40
 */

namespace App\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class TinymceType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'theme' => 'simple',
            'attr' => function (Options $options) {
                return array('class' => 'tinymce', 'data-theme' => $options['theme']);
            }
        ));
        $resolver->setAllowedValues(array(
            'theme' => array('simple', 'medium', 'advanced', 'bbcode'),
        ));
    }

    public function getName()
    {
        return 'tinymce';
    }

    public function getParent()
    {
        return 'textarea';
    }
}