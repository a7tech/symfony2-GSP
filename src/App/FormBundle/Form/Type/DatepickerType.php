<?php
/**
 * DatepickerType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 30.07.13 16:39
 */

namespace App\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatepickerType extends AbstractType
{
    public function getParent()
    {
        return 'date';
    }

    public function getName()
    {
        return 'datepicker';
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $css_class = 'datepicker';
        $view->vars['attr']['class'] = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'].' '.$css_class : $css_class;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => 'dd-MM-yyyy',
        ));
    }
}