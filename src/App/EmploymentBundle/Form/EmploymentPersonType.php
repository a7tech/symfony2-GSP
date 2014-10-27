<?php

namespace App\EmploymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EmploymentPersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('department', null,
                  array('required'=>false,
                        'empty_value' => 'Choose an option',
                        'attr'=>array('class'=>'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.title', 'ASC');
                        return $qb;
                    }
                        ))
            ->add('role', null, array('required'=>true))
            ->add('company', 'company_single_autocomplete',array('required'=>true))
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
        return 'employment_person_form';
    }

}
