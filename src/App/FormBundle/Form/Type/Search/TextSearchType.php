<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 22.01.14
 * Time: 02:25
 */

namespace App\FormBundle\Form\Type\Search;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TextSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('operation', 'choice', [
            'label' => false,
            'choices' => [
                'starts_with' => 'Starts with',
                'doesnt_start_with' => 'Doesn\'t start with',
                'equals' => 'Is equal to',
                'contains' => 'Contains'
            ]
        ])->add('value', 'text', [
            'label' => false,
            'required' => false
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'text_search';
    }

} 