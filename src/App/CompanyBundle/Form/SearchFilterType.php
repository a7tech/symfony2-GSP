<?php
/**
 * CompanyType
 *
 * @author Ricardo Renteria
 */

namespace App\CompanyBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchFilterType extends AbstractType
{
    public $em;
    public $sector;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dql1 = "SELECT c from AppAddressBundle:Country c where c.name='Canada' or c.name='United States' ";
        $res1 = $this->em->createQuery($dql1)->getResult();

        $countryList = array();
        $socialMediaTypeList = array();

        foreach($res1 as $val1){
            $countryList[] = $val1;
        }
 
        $builder->add('name', null, array('label'=>'Name', 'required' => false))
            ->add('companyType', 'entity', array(
                'class'=>'AppCompanyBundle:CompanyType',
                'label'=>'Copmany Profile',
                'required' => false,
                'empty_value'=>'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'query_builder'=>function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('sp')
                        ->orderBy('sp.name', 'ASC');
                        return $qb;
                },
                'property'  => 'name',
                ))
            ->add('sector', 'entity', array(
                'class' => 'App\IndustryBundle\Entity\Sector',
                'label'=>'Industry sector',
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('se')
                            ->orderBy('se.title', 'ASC');
                },
                'property'  => 'title',
                'required'=>false
                ))
            ->add('specialities', 'entity', array(      
                'required'  => false,
                'class' => 'App\IndustryBundle\Entity\Speciality',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('s')->innerJoin('s.sector', 'se')
                        ->orderBy('s.title', 'ASC');
                },
                'attr'=>array('class'=>'form-control'),
                'expanded'=>false,
                'multiple'=>false,
                'property'  => 'title',
                'empty_value' => 'Choose an option',
                'label' => 'Industry specialty'
                ))
            ->add('phoneType', 'entity', array(      
                    'required'  => false,
                    'class' => 'App\PhoneBundle\Entity\PhoneType',
                    'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('p')
                            ->orderBy('p.name', 'ASC');
                    },
                    'attr'=>array('class'=>'form-control'),
                    'property'  => 'name',
                    'empty_value' => 'Choose an option',
                    'label'     => 'Phone type'
                    )
                )

            ->add('addressType', 'entity', array(      
                    'required'  => false,
                    'class' => 'App\AddressBundle\Entity\AddressType',
                    'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('at')
                            ->orderBy('at.name', 'ASC');
                    },
                    'attr'=>array('class'=>'form-control'),
                    'property'  => 'name',
                    'empty_value' => 'Choose an option',
                    'label'     => 'Address type'
                    )
                )

            ->add('street', null, array('label'=>'Street address', 'required' => false))

            ->add('country', 'entity', array(
                'class' => 'App\AddressBundle\Entity\Country',
                'label'=>'Country',
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'preferred_choices'=> $countryList,
                'required'=>false
            ))
            ->add('state', null, array('label'=>'Province/State', 'required' => false))
            ->add('city', null, array('label'=>'City', 'required' => false))
            
            ->add('postcode', null, array('label'=>'Postal/Zip code', 'required' => false))
            ->add('socialMediaType', 'entity', array(
                'class' => 'App\SocialMediaBundle\Entity\SocialMediaType',
                'label'=>'Social media type',
                'empty_value' => 'Choose an option',
                'attr'=>array('class'=>'form-control'),
                'required'=>false,
                'query_builder'=>function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('smt')
                        ->orderBy('smt.name', 'ASC');
                        return $qb;
                },
                'property'  => 'name',
            ))

            // ->add('createdAtFrom', 'datepicker', array('label' => 'Creation Date From', 'required' => false))
            // ->add('createdAtTo', 'datepicker', array('label' => 'Creation Date To', 'required' => false))

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