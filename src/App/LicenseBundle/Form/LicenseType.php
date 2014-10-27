<?php

namespace App\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
class LicenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('licenseType', null,
                  array(
                        'label'=>'License Type',
                        'empty_value'=>'Choose an option',
                        'attr'=>array('class'=>'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }
                        ))
            ->add('code', null, array('attr'=>array('class'=>'input-medium')))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\LicenseBundle\Entity\License'
        ));
    }

    public function getName()
    {
        return 'app_licensebundle_licensetype';
    }
}
