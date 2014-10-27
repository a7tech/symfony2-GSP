<?php

namespace App\EmploymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EmploymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('department', null,
                  array(
                        'empty_value' => 'Choose an option',
                        'attr'=>array('class'=>'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.title', 'ASC');
                        return $qb;
                    }
                        ))
            ->add('role')
            ->add('person', 'person_autocomplete', array('required'=>true,'attr'=>array('class'=>'form-control')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\EmploymentBundle\Entity\Employment'
        ));
    }

    public function getName()
    {
        return 'app_employmentbundle_employmenttype';
    }

}
