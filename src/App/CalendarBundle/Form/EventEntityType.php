<?php

namespace App\CalendarBundle\Form;

use App\UserBundle\Entity\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventEntityType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('startDatetime', 'date', array(
                'label' => 'Start date',
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'datetimepicker')))
            ->add('endDatetime', 'date', array(
                'label' => 'End date',
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'datetimepicker')))
            ->add('allDay')
            ->add('description', 'textarea', [
                'required' => false
            ])->add('user', 'entity', [
                'class' => 'App\UserBundle\Entity\User',
                'query_builder' => function(UserRepository $repository){
                        return $repository->getDefaultQueryBuilder()->orderBy($repository->column('username'));
                    },
                'empty_value' => 'Assign to me',
                'required' => false,
                'attr' => array('class' => 'form-control')
            ]);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CalendarBundle\Entity\EventEntity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_calendarbundle_evententity';
    }
}
