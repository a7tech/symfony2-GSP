<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 20.02.14
 * Time: 17:21
 */

namespace App\InvoiceBundle\Form\Type;


use App\InvoiceBundle\Entity\SaleOrder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TasksType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var SaleOrder $sale_order */
        $sale_order = $form->getParent()->getData();

        $project = $sale_order->getProject();
        $category = $sale_order->getProjectCategory();
        if($category !== null){
            $view->vars['tasks_categories'] = $project->getCategories(true, $this->entityManager)[$category->getId()];
        }

        $forms_by_ids = [];
        foreach($form->all() as $index => $child){
            $child_id = $child->getData()->getTask()->getId();
            $forms_by_ids[$child_id] = $view->children[$index];
        }

        $view->vars['child_forms'] = $forms_by_ids;
        $view->vars['in_category'] = $category !== null;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'type' => 'invoice_task',
            'show_add' => false,
            'attr' => [
                'class' => 'tasks-items'
            ]
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'invoice_tasks';
    }

    public function getParent()
    {
        return 'collection';
    }


} 