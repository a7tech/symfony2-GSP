<?php
/**
 * CategoryAutocreateListener
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 21.08.13 15:16
 */

namespace App\SkillBundle\Form\EventListener;

use App\SkillBundle\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CategoryAutocreateListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var Slugify
     */
    protected $slugify;

    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, Slugify $slugify)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->slugify = $slugify;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND => 'preBind',
        );
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (!empty($data['category']) && is_string($data['category']) && !is_numeric($data['category'])) {
            $category = $this->createCategory($data);
            $data['category'] = $category->getId();
        }

        $event->setData($data);
    }

    /**
     * createCategory
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data)
    {
        $user = $this->securityContext->getToken()->getUser();
        $sector = $this->em->getRepository('AppIndustryBundle:Sector')->find($data['sector']);
        $speciality = $this->em->getRepository('AppIndustryBundle:Speciality')->find($data['speciality']);
        $title = $data['category'];
        $slug = $this->slugify->slugify($title);

        $category = new Category();
        $category->setUser($user);
        $category->setSector($sector);
        $category->setSpeciality($speciality);
        $category->setTitle($title);
        $category->setSlug($slug);

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }
}