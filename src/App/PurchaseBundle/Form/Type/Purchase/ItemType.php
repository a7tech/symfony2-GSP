<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.01.14
 * Time: 19:52
 */

namespace App\PurchaseBundle\Form\Type\Purchase;


use App\CompanyBundle\Entity\CompanyRepository;
use App\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use App\PurchaseBundle\Form\Subscriber\PurchaseItemSubscriber;
use App\PurchaseBundle\Form\Subscriber\PurchaseProductTaxesSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('quantity', null, [
            'label' => false,
            'attr' => [
                'class' => 'quantity input-mini'
            ]
        ])->add('price', null, [
            'label' => false,
            'attr' => [
                'class' => 'price input-mini'
            ]
        ])->add('accountProduct', 'hidden', [
            'label' => false,
            'attr' => [
                'class' => 'product'
            ]
        ]);

        $builder->addEventSubscriber(new PurchaseItemSubscriber($this->entityManager))
                ->addEventSubscriber(new PurchaseProductTaxesSubscriber($this->entityManager));

        $builder->get('accountProduct')->addModelTransformer(new EntityToIdTransformer($this->entityManager->getRepository('AppAccountProductBundle:AccountProduct')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\PurchaseBundle\Form\PurchaseWrapper\Item'
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_purchase_item';
    }

} 