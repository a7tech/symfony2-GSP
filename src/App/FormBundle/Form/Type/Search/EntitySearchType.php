<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 22.01.14
 * Time: 02:17
 */

namespace App\FormBundle\Form\Type\Search;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntitySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('operation', 'choice', [
            'label' => false,
            'choices' => [
                'in' => 'Is',
                'not_in' => 'Is not'
            ]
        ])->add('value', 'entity', array_merge([
            'label' => false,
            'multiple' => true,
            'required' => false,
            'select2' => true
        ], $options['entity_options']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'entity_options' => []
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'entity_search';
    }

} 