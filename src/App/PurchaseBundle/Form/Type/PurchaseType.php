<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.01.14
 * Time: 13:52
 */

namespace App\PurchaseBundle\Form\Type;


use App\AccountBundle\Entity\AccountProfileRepository;
use App\PurchaseBundle\Entity\Purchase;
use App\PurchaseBundle\Form\Subscriber\PurchaseItemsSubscriber;
use App\PurchaseBundle\Form\Type\Purchase\ItemType;
use App\PurchaseBundle\Form\Type\Purchase\PaymentsType;
use App\StatusBundle\Utils\StatusTranslator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PurchaseType extends AbstractType
{
    /**
     * @var StatusTranslator
     */
    protected $statusTranslator;

    public function __construct(StatusTranslator $statusTranslator)
    {
        $this->statusTranslator = $statusTranslator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $statuses = $this->statusTranslator->getStatusesNames(Purchase::STATUSES_GROUP_NAME);

        $builder->add('accountProfile', 'entity', [
            'class' => 'AppAccountBundle:AccountProfile',
            'attr' => ['class' => 'form-control'],
            'query_builder' => function(AccountProfileRepository $repository){
                return $repository->getDefaultQueryBuilder();
            }
        ])->add('items', 'collection', [
            'type' => 'backend_purchase_item',
            'allow_add' => true,
            'allow_delete' => true,
            'show_add' => false,
            'label' => false,
            'error_bubbling' => false,
            'attr' => [
                'class' => 'purchase-items'
            ]
        ])->add('isDraft', 'hidden', [
                'attr' => [
                    'class' => 'purchase-is-draft'
                ]
            ]);

        $builder->addEventSubscriber(new PurchaseItemsSubscriber());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\\PurchaseBundle\\Form\\PurchaseWrapper'
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_purchase';
    }

} 