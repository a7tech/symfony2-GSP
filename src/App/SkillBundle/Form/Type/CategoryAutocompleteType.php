<?php
/**
 * CategoryAutocompleteType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 20.08.13 17:46
 */

namespace App\SkillBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CategoryAutocompleteType extends AbstractType
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $user = $this->securityContext->getToken()->getUser();

        $resolver->setDefaults(array(
            'class' => 'App\SkillBundle\Entity\Category',
            'empty_value' => 'Choose a category',
            'query_builder' => function(EntityRepository $repository) use ($user) {
                $qb = $repository->createQueryBuilder('c')->orderBy('c.title', 'ASC');
                $qb->where('c.user IS NULL');
                if ($user) {
                    $qb->orWhere('c.user = :user')->setParameter('user', $user);
                }
                return $qb;
            }
        ));
    }

    public function getName()
    {
        return 'skill_category_autocomplete';
    }

    public function getParent()
    {
        return 'entity';
    }
}