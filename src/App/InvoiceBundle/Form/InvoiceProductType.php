<?php

namespace App\InvoiceBundle\Form;

use App\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use App\InvoiceBundle\Form\Subscriber\InvoiceProductTaxesSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class InvoiceProductType extends AbstractType
{

    protected $entityManager;

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
        $builder
            ->add('product', 'hidden', [
                'label' => false,
                'attr' => [
                    'class' => 'product'
                ]
            ])
            ->add('quantity', null, array('required'=>true, 'attr'=>array('class'=>'quantity input-smini'), 'label'=>false))
            ->add('price', null, array('required'=>true, 'attr'=>array('class'=>'price input-mini'), 'label'=>false))
            ->add('discount', 'percent', array( 'attr'=>array('class'=>'discount input-smini'), 'label'=>false));

        $builder->get('product')->addModelTransformer(new EntityToIdTransformer($this->em->getRepository('AppAccountProductBundle:AccountProduct')));
        $builder->addEventSubscriber(new InvoiceProductTaxesSubscriber($this->em));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\InvoiceBundle\Entity\InvoiceProduct'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'invoice_product';
    }
}
