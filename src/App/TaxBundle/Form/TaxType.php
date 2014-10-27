<?php

namespace App\TaxBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TaxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxType', null,
                  array(
                        'label'=>'Tax Type',
                        'empty_value' => 'Choose an option',
                        'attr'=>array('class'=>'input-large'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }))
            ->add('code', null, array('attr'=>array('class'=>'input-medium')))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TaxBundle\Entity\Tax'
        ));
    }

    public function getName()
    {
        return 'app_taxbundle_taxtype';
    }
}
