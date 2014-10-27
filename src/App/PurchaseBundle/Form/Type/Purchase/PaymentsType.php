<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 28.01.14
 * Time: 20:32
 */

namespace App\PurchaseBundle\Form\Type\Purchase;


use App\CoreBundle\Utils\ObjectsUtils;
use App\PurchaseBundle\Form\Subscriber\SupplierPaymentsSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PaymentsType extends AbstractType
{
    protected $prototype;

    /**
     * @var EntityManager
     */
    protected $entity_manager;

    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->prototype = $builder->create('__supplier__', new SupplierPaymentsType())->getForm();

        $suppliers = $this->entity_manager->getRepository('AppCompanyBundle:Company')->getAll();
        $builder->addEventSubscriber(new SupplierPaymentsSubscriber($suppliers));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototype'] = $this->prototype->createView($view);
        $view->vars['attr']['data-id'] = $this->getName();
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_purchase_payments';
    }

} 