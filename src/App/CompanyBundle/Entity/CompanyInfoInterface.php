<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.02.14
 * Time: 14:06
 */

namespace App\CompanyBundle\Entity;


interface CompanyInfoInterface
{

    public function getName();

    public function getRbq();

    public function getBillingAddress();

    public function getMainImage();
}