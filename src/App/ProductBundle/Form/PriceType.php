<?php
/**
 * PriceType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 29.07.13 17:53
 */

namespace App\ProductBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PriceType extends AbstractType
{
    public $em;
    public $profileId;

    public function __construct(EntityManager $em, $profileId=null) {

        $this->em = $em;
        $this->profileId = $profileId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->profileId) {
            $currency_sql = "SELECT c from AppCurrencyBundle:Currency c
                    INNER JOIN c.accountCurrencies a
                    WHERE a.account=".$this->profileId."
                    AND a.isDefault=1";
        }
        else {
            $currency_sql = "SELECT c from AppCurrencyBundle:Currency c
                    where c.code='CAD' or c.code='USD' or c.code='EUR' ";
        }

        $results1 = $this->em->createQuery($currency_sql)->getResult();

        $preferedIsoChoices = array();

        foreach($results1 as $result) {
            $preferedIsoChoices[] = $result;
        }


        $builder
            ->add('currency', 'entity',
                array(
                    'class'=>'AppCurrencyBundle:Currency',
                    'query_builder'=>function(EntityRepository $repository) {

                            if ($this->profileId) {
                                $qb = $repository->createQueryBuilder('c')
                                    ->innerJoin('c.accountCurrencies', 'a')
                                    ->where('a.account='.$this->profileId)
                                    ->orderBy('c.name', 'ASC');
                                return $qb;
                            }
                            else {
                                $qb = $repository->createQueryBuilder('sp')
                                    ->orderBy('sp.name', 'ASC');
                                return $qb;
                            }
                        },
                    'preferred_choices'=>$preferedIsoChoices,
                    'attr' => ['class' => 'form-control']
                ))
            ->add('listPrice', 'number', array('label' => 'List Price'))
            ->add('purchasePrice', 'number', array('label' => 'Purchase Price'))
            ->add('sellPrice', 'number', array('label' => 'Sell Price'))
            ->add('calcOnSellPrice', 'checkbox', array('label' => 'Dont apply calculations on Sell Price', 'required' => false,'attr'=>array('class' => 'checkbox')))
            ->add('specialPrice', 'number', array('label' => 'Special Price'))
            ->add('sppDateFrom', 'datepicker', array('label' => 'Special Price From Date', 'required' => false))
            ->add('sppDateTo', 'datepicker', array('label' => 'Special Price To Date', 'required' => false))
            ->add('dontShowOnFront', 'checkbox', array('label' => 'Dont show sell price on front', 'required' => false))
            ->add('employeeCommision', 'number', array('label' => 'Employee Commision', 'required' => false))
            ->add('discaunt', 'percent', array('type'=>'integer'))
        ;
    }

    public function getName()
    {
        return 'price_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProductBundle\Entity\Price'
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['embed_form'] = true;
    }
}