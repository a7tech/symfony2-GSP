<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 04.02.14
 * Time: 18:14
 */

namespace App\FormBundle\Form\Type\Search;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumberSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('operation', 'choice', [
            'label' => false,
            'attr' => ['class' => 'form-control'],
            'choices' => [
                'less_equal_than' => '<=',
                'more_equal_than' => '>=',
                'equal' => 'equal',
                'not_equal' => 'not equal'
            ]
        ]);

        if($options['choices'] !== null){
            $builder->add('value', 'choice',
            [
                'choices' => $options['choices'],
                'attr' => ['class' => 'form-control'],
            ]);
        } else {
            $builder->add('value', null, array_merge([
                'label' => false,
                'attr' => ['class' => 'form-control'],
            ], $options['value_options']));
        }
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'choices' => null,
            'value_options' => []
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'number_search';
    }

} 