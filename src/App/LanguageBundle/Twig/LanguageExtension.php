<?php
/**
 * A Twig Language extension that retrieves available languages from the database
 * and returns them to the twig template for use in a language switcher
 *
 * @author Arend-Jan Tetteroo <dev@arendjantetteroo.nl>
 */
namespace App\LanguageBundle\Twig;
 
use App\LanguageBundle\Locale\AvailableLocalesProvider;
use Symfony\Component\Intl\Intl;

class LanguageExtension extends \Twig_Extension
{
    /**
     * Entity Manager
     * 
     * @var AvailableLocalesProvider
     */
    protected  $localesProvider;

    /**
     * Construct the LanguageExtension
     *
     * @param \App\LanguageBundle\Locale\AvailableLocalesProvider $localesProvider
     */
    public function __construct(AvailableLocalesProvider $localesProvider) {
        $this->localesProvider = $localesProvider;
    }
 
    /**
     * Attach the languages() function to twig
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'languages' => new \Twig_Function_Method($this, 'getLanguages'),
        );
    }


    /**
     * Get the available languages from the database
     * @return array Locales
     */
    public function getLanguages()
    {
        return $this->localesProvider->getAvailableBackendLocales();
    }

    /**
     * Get the name of this extension
     * @return string
     */
    public function getName()
    {
        return 'app_languagebundle_language_extension';
    }
}