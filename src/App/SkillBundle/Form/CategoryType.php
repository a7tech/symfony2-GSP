<?php

/**
 * SkillCategoryType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 15.08.13 17:57
 */

namespace App\SkillBundle\Form;

use App\IndustryBundle\Entity\Sector;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
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

    /**
     * @var RouterInterface
     */
    protected $sector;

    public function __construct(EntityManager $em, Sector $sector = null)
    {
        $this->em     = $em;
        $this->sector = $sector;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em     = $this->em;
        $sector = $this->sector;

        $builder
                ->add('sector', 'entity', array(
                    'class'         => 'App\IndustryBundle\Entity\Sector',
                    'empty_value'   => 'Choose a sector',
                    'attr' => array('class' => 'form-control'),
                    'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('n')->orderBy('n.title', 'ASC');
            }
                ))
                ->add('speciality', 'entity', array(
                    'class'         => 'App\IndustryBundle\Entity\Speciality',
                    'empty_value'   => 'Choose a speciality',
                    'attr' => array('class' => 'form-control'),
                    'query_builder' => function(EntityRepository $repository) use ($sector) {
                if ($sector) {
                    $qb = $repository->createQueryBuilder('sp')
                            ->innerJoin('sp.sector', 'se');

                    if ($sector instanceof Sector) {
                        $qb = $qb->where('sp.sector = :sector')
                                ->setParameter('sector', $sector)->orderBy('sp.title', 'ASC');
                    } elseif (is_numeric($sector)) {
                        $qb = $qb->where('se.id = :id')
                                ->setParameter('id', $sector)->orderBy('sp.title', 'ASC');
                    }
                    return $qb;
                } else {
                    return $repository->createQueryBuilder('n')->orderBy('n.title', 'ASC');
                }
            }
                ))
                ->add('parent', 'select_category', array('class' => $options['data_class'], 'required' => false,'attr' => array('class' => 'form-control')))
                    
                ->add('title')
                ->add('slug')
                ->add('description', 'textarea', array(
                    'attr' => array(
                        'class'      => 'tinymce',
                        'data-theme' => 'simple' // simple, medium, advanced, bbcode
                    )
                ))
                ->add('useForEvaluation', 'checkbox', array('label' => 'Use for evaluation', 'required' => false))
                ->add('value', 'integer')
                ->add('position', 'integer')
        ;

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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\SkillBundle\Entity\Category'
        ));
    }

    public function getName()
    {
        return 'app_skillbundle_categorytype';
    }

}