<?php
/**
 * ProductType
 *
 * @author Ricardo Renteria
 */

namespace App\PersonBundle\Form;

use App\AddressBundle\Entity\Country;
use App\AddressBundle\Entity\Province;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SearchFilterType extends AbstractType
{
    public $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $dql1 = "SELECT c from AppAddressBundle:Country c
                    where c.name='Canada' or c.name='United States' ";
        $results1 = $this->em->createQuery($dql1)->getResult();

        $preferedIsoChoices = array();

        foreach($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }

        $builder->add('firstName', null, array('label'=>'First Name', 'required' => false))
            ->add('lastName', null, array('label'=>'Last Name', 'required' => false))
            ->add('company', null ,array('required'=>false))
            ->add('email', null, array('label'=>'Email address', 'required' => false))
            ->add('gender', 'choice', array(
                'choices'=> array('m'=>'Male', 'f'=>'Femaile'),
                'empty_value'=>'Choose an option',
                'required'=>false,
                'preferred_choices'=>array('empty_value'),
                'attr'=> ['class' => 'form-control'],
            ))
            ->add('language', 'entity', array(
                'empty_value' => 'Choose a language',
                'class' => 'AppLanguageBundle:Language',
                'required'=>false,
                'attr'=> ['class' => 'form-control'],
                'query_builder'=>function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('n')->orderBy('n.name', 'ASC');
                }
            ))
            ->add('phones', null, array('label'=>'Phone', 'required' => false))
            ->add('street', null, array('label'=>'Address', 'required' => false))
            ->add('city', null, array('label'=>'City', 'required' => false))

            ->add('country', 'entity', array(
                'class' => 'App\AddressBundle\Entity\Country',
                'label'=>'Country',
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'preferred_choices'=> $preferedIsoChoices,
                'required'=>false
            ))
            // ->add('creationDateFrom', 'datepicker', array('label' => 'Creation date from', 'required' => false))
            // ->add('creationDateTo', 'datepicker', array('label' => 'Creation date to', 'required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'csrf_protection'   => false,
                'validation_groups' => array('filtering')
        ));
    }
    
    public function getName()
    {
        return 'search';
    }
   
}