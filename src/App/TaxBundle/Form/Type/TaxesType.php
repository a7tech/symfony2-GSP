<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 12.02.14
 * Time: 23:05
 */

namespace App\TaxBundle\Form\Type;


use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaxesType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $data_by_ids = [];
        foreach($options['choices'] as $data_item){
            $data_by_ids[$data_item->getId()] = $data_item;
        }

        foreach($view->children as $child){
            /** @var FormView $child */
            $child->vars['attr']['data-tax'] = $data_by_ids[$child->vars['value']]->getTaxType()->getRate();
            $css_class = 'tax';
            $child->vars['attr']['class'] = isset($child->vars['attr']['class']) ? $child->vars['attr']['class'] .' '.$css_class : $css_class;
        }
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => true,
        ]);

        $resolver->setAllowedValues([
            'expanded' => [true],
            'multiple' => [true]
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'taxes';
    }

    public function getParent()
    {
        return 'entity_sorted';
    }


} 