<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 22.01.14
 * Time: 03:49
 */

namespace App\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChoiceExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if($options['select2'] === true){
            $select2_class = 'select2';
            $view->vars['attr']['class'] = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'].' '.$select2_class : $select2_class;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'select2' => false
        ]);
    }


    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'choice';
    }

} 