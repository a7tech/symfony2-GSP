<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 24.02.14
 * Time: 15:32
 */

namespace App\InvoiceBundle\Form\Type;


use App\TaskBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotInvoicedTasks extends TaskSelectType
{


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $css_class = 'not-invoiced-tasks';
        $view->vars['attr']['class'] = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'].' '.$css_class : $css_class;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $class = 'AppTaskBundle:Task';

        $resolver->setDefaults([
            'project' => null,
            'class' => $class,
            'choices' => function(Options $options) use ($class) {
                $this->tasks = $this->entityManager->getRepository($class)->getNotInvoicedExtraTasks($options['project']);
                return $this->tasks;
            }
        ]);

        $resolver->setAllowedTypes([
            'project' => '\App\ProjectBundle\Entity\Project'
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'invoice_not_invoiced_tasks';
    }

    public function getParent()
    {
        return 'entity';
    }


} 