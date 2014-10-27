<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.01.14
 * Time: 11:21
 */

namespace App\CoreBundle\Utils;


class Formatter
{
    /**
     * Changes underscores to camel case
     *
     * @param 	string 	$str
     * @return	string
     */
    static public function toCamelCase($str){
        $arrUnderscoreChars = array('_a','_b','_c','_d','_e','_f','_g','_h','_i','_j','_k','_l','_m','_n','_o','_p','_q','_r','_s','_t','_u','_w','_v','_x','_y','_z');
        $arrCamelCaseChars = array('A','B','C','D','E','F','G','H','I','j','K','L','M','N','O','P','Q','R','S','T','U','W','V','X','Y','Z');

        return lcfirst(str_replace($arrUnderscoreChars, $arrCamelCaseChars, $str));
    }

    /**
     * Convert a string in camelCase to underscores, e.g. uploadFile to upload-file.
     *
     * @param string $string
     * @return string
     */
    public static function toUnderscores($string) {
        return self::convertCamelCase($string, '_');
    }

    /**
     * Convert a string in camelCase to hyphens, e.g. uploadFile to upload-file.
     *
     * @param string $string
     * @return string
     */
    public static function toHyphenated($string) {
        return self::convertCamelCase($string, '-');
    }

    /**
     * Convert a string in camelCase to one sepatared by given char ($separator) and lowercased, e.g. uploadFile to upload?file.
     *
     * @param string $string
     * @param $separator
     * @internal param string $hyphen Character to join the words
     * @return string
     */
    public static function convertCamelCase($string, $separator){
        $string = preg_replace('/([a-z])([A-Z])/', '$1'. $separator .'$2', $string);
        $string = strtolower($string);
        return $string;
    }

    /**
     * Formats a string with underscores, dashes and dots to a nice readable name.
     * Use to format service names or array keys or settings keys, etc.
     *
     * @param string $name String to be formatted.
     * @return string
     */
    static public function toReadableName($name) {
        $name = str_replace(array('-', '_', '.'), ' ', $name);
        $name = ucwords($name);
        return $name;
    }
} 