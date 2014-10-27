<?php

namespace App\SocialMediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SocialMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('socialMediaType', null,
                  array(
                        'label'=>'Social Media Type',
                        'empty_value' => 'Choose an option',
                        'attr'=>array('class'=>'form-control'),
                        'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }))
            ->add('content', null, array('attr'=>array('class'=>'input-medium')))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\SocialMediaBundle\Entity\SocialMedia'
        ));
    }

    public function getName()
    {
        return 'social_media_form';
    }
}
