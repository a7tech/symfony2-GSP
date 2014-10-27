<?php

namespace App\AccountBundle\Form;

use App\CoreBundle\Entity\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountCurrencyType extends AbstractType
{
    public $em;

    public function __construct(EntityManager $em)
    {

        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currency_sql = "SELECT c from AppCurrencyBundle:Currency c
                    where c.code='CAD' or c.code='USD' or c.code='EUR' ";
        $results1 = $this->em->createQuery($currency_sql)->getResult();

        $preferedIsoChoices = array();

        foreach ($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }

        $builder
            ->add('currency', 'entity', array(
                'class' => 'AppCurrencyBundle:Currency',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('sp')
                            ->orderBy('sp.name', 'ASC');
                        return $qb;
                    },
                'preferred_choices' => $preferedIsoChoices
            ))
            ->add('isDefault')
            ->add('rate');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AccountBundle\Entity\AccountCurrency'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_accountbundle_accountcurrency';
    }
}
