<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 10.02.14
 * Time: 16:21
 */

namespace App\CoreBundle\Twig\Extension;


class NumberExtension extends \Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_number';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('number_format', array($this, 'app_number_format_filter'), array('is_safe' => array('html')))
        ];
    }

    function app_number_format_filter($number, $decimal = 0, $decimalPoint = '.', $thousandSep = '&nbsp;')
    {
        return number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
    }

} 