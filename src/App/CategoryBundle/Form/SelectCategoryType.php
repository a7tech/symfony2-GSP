<?php
/**
 * CategorySelectType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 08.07.13 15:34
 */

namespace App\CategoryBundle\Form;

use App\CategoryBundle\Entity\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class SelectCategoryType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $em = $this->em;

        $choices = function(Options $options) use ($em) {
            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = $em->getRepository($options['class']);
            return $categoryRepository->getList();
        };

        $resolver->setDefaults(array(
            'empty_value' => 'Root',
            'choices' => $choices,
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'select_category';
    }
}