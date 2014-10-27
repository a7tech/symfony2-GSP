<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-14
 * Time: 20:35
 */

namespace App\InvoiceBundle\Entity\Returns;

use App\InvoiceBundle\Entity\InvoiceTask;

class ReturnType
{
    const RETURN_OF_ITEM = 0;
    const REFUND = 1;

    protected static $types = [
        self::RETURN_OF_ITEM => 'return',
        self::REFUND => 'refund'
    ];

    public static function getTypes()
    {
        return array_keys(self::$types);
    }

    public static function getTypesNames($prefix = null)
    {
        if($prefix === null) {
            return self::$types;
        } else {
            $types = [];
            foreach(self::$types as $key => $name){
                $types[$key] = $prefix.$name;
            }

            return $types;
        }
    }

    public static function getTypeName($type)
    {
        return self::$types[$type];
    }
} 