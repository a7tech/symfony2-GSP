<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.01.14
 * Time: 19:59
 */

namespace App\FormBundle\Form\Extension;


use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['show_add'] = $options['show_add'];
        $css_class = 'collection';

        $view->vars['attr']['class'] = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'].' '.$css_class : $css_class;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'show_add' => true
        ]);
    }


    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'collection';
    }

} 