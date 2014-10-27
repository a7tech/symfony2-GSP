<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 17.01.14
 * Time: 05:35
 */

namespace App\StatusBundle\Twig\Extension;


use App\StatusBundle\Utils\StatusTranslator;

class StatusExtension extends \Twig_Extension
{
    /**
     * @var \App\StatusBundle\Utils\StatusTranslator
     */
    protected $translator;

    public function __construct(StatusTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
           new \Twig_SimpleFunction('statusInfo', array($this, 'statusInfo'))
        ];
    }

    public function statusInfo($className, $value)
    {
        if(is_object($className)){
            $className = get_class($className);
        }

        return $this->translator->getStatusInfo($className, $value);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'status';
    }

} 