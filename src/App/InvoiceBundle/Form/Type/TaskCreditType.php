<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-07-24
 * Time: 21:34
 */

namespace App\InvoiceBundle\Form\Type;


use App\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskCreditType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('refund', null, [
            'label' => 'task_credit',
            'attr' => [
                'class' => 'credit-price input-small'
            ]
        ])->add('refundDescription', 'textarea', [
            'label' => 'credit_description',
            'required' => false,
            'attr' => [
                'style' => 'resize: vertical',
                'class' => 'credit-description',
            ]
        ])->add('task', 'hidden', [
            'label' => false,
            'attr' => [
                'class' => 'task'
            ]
        ]);

        $builder->get('task')->addModelTransformer(new EntityToIdTransformer($this->entityManager->getRepository('AppTaskBundle:Task')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\InvoiceBundle\Entity\InvoiceTask',
            'translation_domain' => 'Invoice',
            'invoice_tasks' => [],
            'error_bubbling' => false,
            'cascade_validation' => true
        ]);
    }

    public function getName()
    {
        return 'backend_invoice_task_return';
    }
} 