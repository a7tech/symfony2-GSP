<?php

namespace App\LanguageBundle\Router;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Route;

use JMS\I18nRoutingBundle\Router\PatternGenerationStrategyInterface;

/**
 * This Pattern Generation Strategy overrides the i18nrouting bundle one, 
 * as we need to dynamically define the allowed locales. 
 * By default only the locales in the i18n config.yml are allowed, but as we need to add translations 
 * through the database, this doesn't work. This Strategy dynamically loads them from the database
 *
 * @author Arend-Jan Tetteroo <dev@arendjantetteroo.nl
 */
class DefaultPatternGenerationStrategy implements PatternGenerationStrategyInterface
{
    const STRATEGY_PREFIX = 'prefix';
    const STRATEGY_PREFIX_EXCEPT_DEFAULT = 'prefix_except_default';
    const STRATEGY_CUSTOM = 'custom';

    private $strategy;
    private $translator;
    private $translationDomain;
    private $locales;
    private $cacheDir;
    private $defaultLocale;

    public function __construct($strategy, TranslatorInterface $translator, array $locales, $cacheDir, $translationDomain = 'routes', $defaultLocale = 'en', $em)
    {
        $this->strategy = $strategy;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->cacheDir = $cacheDir;
        $this->defaultLocale = $defaultLocale;
        $this->em = $em;

        $this->locales = $this->getLocales();
    }

    /**
     * Get the available locales from the database
     *
     * @todo: Cache this if it gets too slow, by apc
     * @return array locales
     */
    public function getLocales()
    {
        $repo = $this->em->getRepository('AppLanguageBundle:Language');

        return $repo->getLocales();
    }

    /**
     * {@inheritDoc}
     */
    public function generateI18nPatterns($routeName, Route $route)
    {
        $patterns = array();
        foreach ($route->getOption('i18n_locales') ?: $this->locales as $locale) {
            // if no translation exists, we use the current pattern
            if ($routeName === $i18nPattern = $this->translator->trans($routeName, array(), $this->translationDomain, $locale)) {
                $i18nPattern = $route->getPattern();
            }

            // prefix with locale if requested
            if (self::STRATEGY_PREFIX === $this->strategy
                    || (self::STRATEGY_PREFIX_EXCEPT_DEFAULT === $this->strategy && $this->defaultLocale !== $locale)) {
                $i18nPattern = '/'.$locale.$i18nPattern;
            }

            $patterns[$i18nPattern][] = $locale;
        }
        //var_dump($patterns);

        return $patterns;
    }

    /**
     * {@inheritDoc}
     */
    public function addResources(RouteCollection $i18nCollection)
    {
        foreach ($this->locales as $locale) {
            if (file_exists($metadata = $this->cacheDir.'/translations/catalogue.'.$locale.'.php.meta')) {
                foreach (unserialize(file_get_contents($metadata)) as $resource) {
                    $i18nCollection->addResource($resource);
                }
            }
        }
    }
}