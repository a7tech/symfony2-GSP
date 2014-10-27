<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),

            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
//            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            
            # Translations
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),

            # Users
            new FOS\UserBundle\FOSUserBundle(),
            new Spomky\RoleHierarchyBundle\SpomkyRoleHierarchyBundle(),
            new Egulias\SecurityDebugCommandBundle\EguliasSecurityDebugCommandBundle(),

            # Javascript tools
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            #new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),

            #new FOQ\ElasticaBundle\FOQElasticaBundle(),

            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new Zenstruck\SlugifyBundle\ZenstruckSlugifyBundle(),
            new Vlabs\MediaBundle\VlabsMediaBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),

            # Calendar bundle
            new ADesigns\CalendarBundle\ADesignsCalendarBundle(),
            
            # Mopa Bootstrap Bundle
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),

            #local
            new App\UserBundle\AppUserBundle(),
            new App\AccountBundle\AppAccountBundle(),
            new App\AddressBundle\AppAddressBundle(),
            new App\FileBundle\AppFileBundle(),
            new App\PhoneBundle\AppPhoneBundle(),
            new App\EmailBundle\AppEmailBundle(),
            new App\SocialMediaBundle\AppSocialMediaBundle(),
            new App\IndustryBundle\AppIndustryBundle(),
            new App\CompanyBundle\AppCompanyBundle(),
            new App\PersonBundle\AppPersonBundle(),
            new App\LicenseBundle\AppLicenseBundle(),
            new App\EmploymentBundle\AppEmploymentBundle(),
            new App\TaxBundle\AppTaxBundle(),
            new App\ProductBundle\AppProductBundle(),
            new App\CategoryBundle\AppCategoryBundle(),
            new App\CurrencyBundle\AppCurrencyBundle(),
            new App\BarcodeBundle\AppBarcodeBundle(),
            new App\MediaBundle\AppMediaBundle(),
            new App\SkillBundle\AppSkillBundle(),
            new App\CvBundle\AppCvBundle(),
            new App\HrBundle\AppHrBundle(),
            new App\FormBundle\AppFormBundle(),
            new App\BackendBundle\AppBackendBundle(),
            new App\LanguageBundle\AppLanguageBundle(),
            new App\StatusBundle\AppStatusBundle(),
            new App\InvoiceBundle\AppInvoiceBundle(),
            new App\PaymentBundle\AppPaymentBundle(),
            new App\CalendarBundle\AppCalendarBundle(),
            new App\ProjectBundle\AppProjectBundle(),
            new App\TaskBundle\AppTaskBundle(),
            new App\AccountProductBundle\AppAccountProductBundle(),
            new App\CoreBundle\AppCoreBundle(),
            new App\PurchaseBundle\AppPurchaseBundle(),
            new WhiteOctober\TCPDFBundle\WhiteOctoberTCPDFBundle(),
            new App\PlaceBundle\AppPlaceBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Elao\WebProfilerExtraBundle\WebProfilerExtraBundle();
            $bundles[] = new \FirePHPBundle\FirePHPBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
