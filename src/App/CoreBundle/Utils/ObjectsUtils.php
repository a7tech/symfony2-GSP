<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 29.01.14
 * Time: 01:23
 */

namespace App\CoreBundle\Utils;


class ObjectsUtils
{
    public static function extractIds($collection){
        $return = [];

        foreach($collection as $item){
            $return[$item->getId()] = $item;
        }

        return $return;
    }

    public static function extractKeyProperty($collection, $key, $property)
    {
        $array = [];
        $key_function = Formatter::toCamelCase('get_'.$key);
        $value_function = Formatter::toCamelCase('get_'.$property);

        foreach($collection as $item){
            $array[$item->$key_function()] = $item->$value_function();
        }

        return $array;
    }

    public static function getEntitiesToRemove(array $old, array $new)
    {
        return array_udiff($old, $new,
            function($img1, $img2) {
                if ($img1->getId() == $img2->getId()) return 0;
                return $img1->getId() > $img2->getId() ? 1 : -1;
            }
        );
    }
} 