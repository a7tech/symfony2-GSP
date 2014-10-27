<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 19.03.14
 * Time: 12:04
 */

namespace App\FormBundle\Form\Type;


use App\FormBundle\Form\DataTransformer\IntervalTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IntervalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', 'number', [
            'label' => 'days'
        ])->add('hours', 'number', [
            'label' => 'hours'
        ])->add('minutes', 'number', [
            'label' => 'minutes'
        ]);

        $builder->addModelTransformer(new IntervalTransformer($options['working_hours']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'working_hours' => 8
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'interval';
    }


} 