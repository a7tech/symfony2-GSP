<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-05-06
 * Time: 13:30
 */

namespace App\LanguageBundle\Util\DataGrid;

use App\LanguageBundle\Locale\AvailableLocalesProvider;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Lexik\Bundle\TranslationBundle\Document\TransUnit as TransUnitDocument;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitManagerInterface;
use Lexik\Bundle\TranslationBundle\Storage\StorageInterface;

class DataGridRequestHandler
{
    /**
     * @var TransUnitManagerInterface
     */
    protected $transUnitManager;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var AvailableLocalesProvider
     */
    protected $localesProvider;

    /**
     * @var array
     */
    protected $staticManagedLocales;

    /**
     * @var
     */
    protected $managedLocales;

    /**
     * @param TransUnitManagerInterface                           $transUnitManager
     * @param StorageInterface                                    $storage
     * @param \App\LanguageBundle\Locale\AvailableLocalesProvider $localesProvider
     * @param array                                               $staticManagedLocales
     *
     * @internal param array $managedLoales
     */
    public function __construct(TransUnitManagerInterface $transUnitManager, StorageInterface $storage, AvailableLocalesProvider $localesProvider, array $staticManagedLocales)
    {
        $this->transUnitManager = $transUnitManager;
        $this->storage = $storage;
        $this->localesProvider = $localesProvider;
        $this->staticManagedLocales = $staticManagedLocales;
    }

    protected function getManagedLocales($cached = true){
        if($this->managedLocales === null || $cached == false){
            $this->managedLocales = array_unique(array_merge($this->staticManagedLocales, $this->localesProvider->getAvailableBackendLocales()));
        }

        return $this->managedLocales;
    }

    /**
     * Returns an array with the trans unit for the current page and the total of trans units
     *
     * @param Request $request
     * @param bool    $cached_locales
     *
     * @return array
     */
    public function getPage(Request $request, $cached_locales = true)
    {
        $managedLocales = $this->getManagedLocales($cached_locales);

        $transUnits = $this->storage->getTransUnitList(
            $managedLocales,
            $request->query->get('rows', 20),
            $request->query->get('page', 1),
            $request->query->all()
        );

        $count = $this->storage->countTransUnits($managedLocales, $request->query->all());

        return array($transUnits, $count);
    }


    /**
     * Updates a trans unit from the request.
     *
     * @param integer $id
     * @param Request $request
     * @param bool    $useCachedLocales
     *
     * @return \Lexik\Bundle\TranslationBundle\Model\TransUnit
     */
    public function updateFromRequest($id, Request $request, $useCachedLocales = true)
    {
        $transUnit = $this->storage->getTransUnitById($id);

        if (!$transUnit) {
            throw new NotFoundHttpException(sprintf('No TransUnit found for "%s"', $id));
        }

        $translationsContent = array();
        foreach ($this->getManagedLocales($useCachedLocales) as $locale) {
            $translationsContent[$locale] = $request->request->get($locale);
        }

        $this->transUnitManager->updateTranslationsContent($transUnit, $translationsContent);

        if ($transUnit instanceof TransUnitDocument) {
            $transUnit->convertMongoTimestamp();
        }

        $this->storage->flush();

        return $transUnit;
    }
} 