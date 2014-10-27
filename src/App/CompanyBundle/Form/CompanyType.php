<?php

namespace App\CompanyBundle\Form;

use App\IndustryBundle\Entity\Sector;
use App\PersonBundle\Form\PersonType;
use App\ProductBundle\Form\FormMapper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyType extends AbstractType
{

    public   $em;
    public   $sector;

    public function __construct(EntityManager $em, Sector $sector=null)
    {
        $this->em = $em;
        $this->sector = $sector;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currency_sql = "SELECT c from AppCurrencyBundle:Currency c
                    where c.code='CAD' or c.code='USD' or c.code='EUR' ";
        $results1 = $this->em->createQuery($currency_sql)->getResult();

        $preferedIsoChoices = array();

        foreach($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }


        $sector = $this->sector;

        $mapper = new FormMapper($builder);
        $mapper
            ->with('Company info')
            ->add('name', null, array('label' => 'Company Name',  'required' => true))
            ->add('images', 'collection', array(
                'label' => 'Company logo',
                'type' => new CompanyImageType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array('class' => 'input-group collection image-upload'),
                'required' => false
            ))
            ->add('rbq', null, array('label'=>'RBQ'))
            ->add('licenses', 'collection', array(
                'type' => new \App\LicenseBundle\Form\LicenseType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'horizontal-form'),
                'label' => 'Licences, Permits, Authorization, & Insurances',
            ))
            ->add('creationDate', 'date', array(
                'label' => 'Creation Date',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'datepicker')))
            ->add('description', 'textarea', array(
                'label' => 'Company description',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false))
            ->add('sector', null, array('attr' => array('class' => 'form-control shortSelect'), 'empty_value' => 'Choose an option', 'required' => false))
            ->add('specialities', 'entity',
                array(
                    'class'=>'App\IndustryBundle\Entity\Speciality',
                    'required' => false,
                    'empty_value' => 'Choose an option',
                    'expanded'=>true,
                    'multiple'=>true,
                    'attr' => array('class' => 'checkbox'),
                    'query_builder'=>function(EntityRepository $repository) use ($sector) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->innerJoin('sp.sector', 'se');

                        if($sector instanceof Sector) {
                            $qb = $qb->where('sp.sector = :sector')
                                ->setParameter('sector', $sector);
                        } elseif(is_numeric($sector)) {
                            $qb = $qb->where('se.id = :id')
                                ->setParameter('id', $sector);
                        }
                        return $qb;
                    }
                ))
            ->add('companyType', 'entity', array(
                'class'=>'AppCompanyBundle:CompanyType',
                'label'=>'Company Profile',
                'required' => false,
                'empty_value'=>'Choose an option',
                'attr' => array('class' => 'form-control'),
                'query_builder'=>function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('sp')
                        ->orderBy('sp.name', 'ASC');
                    return $qb;
                }))

            ->with('Business groups')

            ->add('groups', null,
                array(
                    'required' => false,
                    'empty_value' => 'Choose an option',
                    'expanded'=>true,
                    'multiple'=>true,
                    'attr' => array('class' => 'checkbox'),
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.title', 'ASC');
                        return $qb;
                    }
                ))
            ->add('categories', 'category_tree', array(
                'class' => 'AppProductBundle:Category',
                'attr' => array('class' => 'checkbox product-categories'),
                'required' => false
            ))
            ->add('brandGroups', 'entity_sorted', array(
                'label' => 'Brand Group',
                'empty_value' => 'Choose a brand group',
                'class' => 'AppProductBundle:BrandGroup',
                'required' => false,
                'expanded'=>true,
                'attr' => array('class' => 'checkbox'),
                'multiple'=>true
            ))
            ->with('Company Details')
            ->add('languages', 'entity',
                array(
                    'class'=>'AppLanguageBundle:Language',
                    'expanded'=>true,
                    'multiple'=>true,
                    'attr' => array('class' => 'checkbox'),
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    }
                ))
            ->add('currencies', 'entity',
                array(
                    'class'=>'AppCurrencyBundle:Currency',
                    'expanded'=>true,
                    'multiple'=>true,
                    'attr' => array('class' => 'checkbox'),
                    'query_builder'=>function(EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    },
                    'preferred_choices'=>$preferedIsoChoices
                ))
            ->add('socialMedias', 'collection', array(
                'type' => new \App\SocialMediaBundle\Form\SocialMediaType(),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'attr'      => array('class' => 'horizontal-form'),
                'label'=>'Social Medias'))
            ->add('phones', 'collection', array(
                'type' => new \App\PhoneBundle\Form\PhoneType($this->em),
                'attr'      => array('class' => 'horizontal-form'),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false))
            ->add('addresses', 'backend_addresses')
            ->with('Employees')
            ->add('employments','collection', array(
                'type' => new \App\EmploymentBundle\Form\EmploymentType(),
                'attr'      => array('class' => 'horizontal-form'),
                'allow_add'=>true,
                'allow_delete'=>true,
                'required'  => false,
                'label'=>false,
                'by_reference' => false,
            ))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CompanyBundle\Entity\Company'
        ));
    }

    public function getName()
    {
        return 'app_companybundle_companytype';
    }
}
