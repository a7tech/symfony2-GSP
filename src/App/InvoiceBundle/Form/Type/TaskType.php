<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 20.02.14
 * Time: 12:28
 */

namespace App\InvoiceBundle\Form\Type;


use App\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use App\InvoiceBundle\Form\Subscriber\TaskTaxesSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('task', 'hidden', [
            'label' => false,
            'attr' => [
                'class' => 'task'
            ]
        ]);

        $builder->get('task')->addModelTransformer(new EntityToIdTransformer($this->entityManager->getRepository('AppTaskBundle:Task')));
        $builder->addEventSubscriber(new TaskTaxesSubscriber($this->entityManager));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\InvoiceBundle\Entity\InvoiceTask'
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'invoice_task';
    }

} 