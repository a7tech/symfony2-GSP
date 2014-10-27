<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.02.14
 * Time: 14:38
 */

namespace App\AccountBundle\Entity;


use App\CompanyBundle\Entity\CompanyInfoInterface;

interface AccountProfileInfoInterface extends CompanyInfoInterface
{
    public function getTaxation();
} 