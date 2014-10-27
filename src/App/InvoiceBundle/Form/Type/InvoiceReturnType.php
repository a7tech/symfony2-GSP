<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-14
 * Time: 20:28
 */

namespace App\InvoiceBundle\Form\Type;


use App\InvoiceBundle\Form\Subscriber\InvoiceReturnSubscriber;
use App\InvoiceBundle\Form\Type\Returns\ProductReturnType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvoiceReturnType extends AbstractType
{
    protected $entityManager;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new InvoiceReturnSubscriber($this->entityManager));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\InvoiceBundle\Entity\SaleOrder',
            'translation_domain' => 'Invoice',
            'error_bubbling' => false,
            'cascade_validation' => true
        ]);
    }



    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_invoice_return';
    }

} 