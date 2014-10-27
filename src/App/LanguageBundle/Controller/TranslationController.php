<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-05-07
 * Time: 20:17
 */

namespace App\LanguageBundle\Controller;

use Lexik\Bundle\TranslationBundle\Controller\TranslationController as BaseController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;

class TranslationController extends BaseController
{
    public function invalidateCacheAction(Request $request)
    {
        $redirect = parent::invalidateCacheAction($request);

        //remove cached JS translation files
        $cache_dir = $this->container->getParameter("kernel.cache_dir").'/bazinga-js-translation';
        if(file_exists($cache_dir)) {
            $finder = new Finder();
            $finder->files()
                ->in($cache_dir);

            foreach ($finder as $file) {
                /** @var SplFileInfo $file */
                unlink($file->getPathname());
            }
        }

        return $redirect;
    }


    protected function getManagedLocales()
    {
        return $this->get('app.locales_provider')->getAvailableBackendLocales();
    }
}