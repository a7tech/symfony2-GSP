<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 19.03.14
 * Time: 13:51
 */

namespace App\CoreBundle\Twig\Extension;


use Symfony\Component\Translation\Translator;

class FormatterExtension extends  \Twig_Extension
{
    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_formatter';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('interval', [$this, 'interval'])
        ];
    }

    public function interval($hours, $working_hours = 8, $short = false)
    {
        if($hours !== null) {
            $interval_days = floor($hours / $working_hours);
            $interval_hours = $hours % $working_hours;
            $interval_minutes = round(fmod($hours, 1)*60);
            $string        = '';

            if ($interval_days > 0) {
                $string = $interval_days . ($short ? '' : ' ') . $this->translator->trans('interval.'.($short ? 'short.' : '').'days', [], 'Common') . ' ';
            }

            if($interval_hours > 0 || ($interval_days > 0 && $interval_minutes > 0)) {
                $string .= $interval_hours . ($short ? '' : ' ') . $this->translator->trans('interval.'.($short ? 'short.' : '').'hours', [], 'Common').' ';
            }

            if($interval_minutes > 0) {
                $string .= $interval_minutes . ($short ? '' : ' ') . $this->translator->trans('interval'.($short ? 'short.' : '').'.minutes', [], 'Common');
            }

            if($string == ''){
                $string = '-';
            }

            return $string;
        } else {
            return '-';
        }
    }


} 