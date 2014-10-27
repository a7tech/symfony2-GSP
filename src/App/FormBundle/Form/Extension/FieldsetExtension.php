<?php
/**
 * FieldsetExtension
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 19.07.13 19:29
 */

namespace App\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldsetExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'group' => null,
        ));
        $resolver->setOptional(array('embed_form'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $group = $options['group'];

        if (null === $group) {
            return;
        }

        $root = $this->getRootView($view);
        $root->vars['groups'][$group][] = $form->getName();
    }

    public function getRootView(FormView $view)
    {
        $root = $view->parent;

        while (null === $root) {
            $root = $root->parent;
        }

        return $root;
    }
}