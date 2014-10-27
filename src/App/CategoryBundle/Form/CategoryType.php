<?php

/**
 * CategoryType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 05.07.13 16:09
 */

namespace App\CategoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManager;

class CategoryType extends AbstractType
{

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->em;
        $builder->add('parent', 'select_category', array('class' => $options['data_class'],'attr' => array('class' => 'form-control'), 'required' => false));
        $builder->add('title');
        $builder->add('slug');
        $builder->add('description', 'textarea', array(
            'attr' => array(
                'class'      => 'tinymce',
                'data-theme' => 'simple' // simple, medium, advanced, bbcode
            )
        ));
        $builder->add('position', 'integer');

        $positionsCountValidator = function(FormEvent $event) use ($em, $options) {
            $form               = $event->getForm();
            $categoryRepository = $em->getRepository($options['data_class']);
            $positionField      = $form->get('position')->getData();
            $parentField        = $form->get('parent')->getData();
            $idEntity           = $form->getData()->getId();
            $children           = $categoryRepository->countChildrenForNode($parentField, $idEntity);

            if ($positionField - $children > 1) {
                $form['position']->addError(new FormError(sprintf("There are only %s positions available in the parent category.", $children)));
            }
        };

        $builder->addEventListener(FormEvents::POST_SUBMIT, $positionsCountValidator);
    }

    public function getName()
    {
        return 'app_categorybundle_categorytype';
    }

}