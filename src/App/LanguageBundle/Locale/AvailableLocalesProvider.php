<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-05-06
 * Time: 13:24
 */

namespace App\LanguageBundle\Locale;


use App\LanguageBundle\Entity\LanguageRepository;
use Doctrine\ORM\EntityManager;

class AvailableLocalesProvider
{
    /** @var EntityManager  */
    protected $entityManager;

    /** @var  LanguageRepository */
    protected $languagesRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAvailableBackendLocales()
    {
        return $this->getLanguagesRepository()->getLocales();
    }

    protected function getLanguagesRepository()
    {
        if($this->languagesRepository === null){
            $this->languagesRepository = $this->entityManager->getRepository('AppLanguageBundle:Language');
        }

        return $this->languagesRepository;
    }
} 