<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 15.01.14
 * Time: 17:31
 */

namespace App\StatusBundle\Entity;


interface StatusesProviderInterface
{
    /**
     * Gets all statuses groups from class
     * Array should be in format [status_group_name => [status_value => status_name, [...]], [...]]
     *
     * @return array
     */
    public static function getStatusesArray();
} 