<?php

namespace App\EmailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailType', null,
                  array(
                        'label'=> 'Email type',
                        'empty_value' => 'Choose an option',
                        'attr'=>array('class'=>'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }))
            ->add('email', 'email', array('required'=>true, 'attr'=>array('class'=>'input-medium')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\EmailBundle\Entity\Email'
        ));
    }

    public function getName()
    {
        return 'email_form';
    }
}
