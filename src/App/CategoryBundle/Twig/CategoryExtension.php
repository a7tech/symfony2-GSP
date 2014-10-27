<?php
/**
 * RepeatFilter
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 08.07.13 23:59
 */

namespace App\CategoryBundle\Twig;


class CategoryExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('repeat', array($this, 'repeatFilter')),
        );
    }

    /**
     * repeatFilter
     *
     */
    public function repeatFilter($string, $multiplier = 2)
    {
        return str_repeat($string, $multiplier);
    }

    /**
     * getName
     *
     */
    public function getName()
    {
        return 'app_categorybundle_extension';
    }
}