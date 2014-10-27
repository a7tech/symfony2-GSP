<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.02.14
 * Time: 14:21
 */

namespace App\TaxBundle\Entity;


interface TaxationInfoInterface {

    public function getName();

    public function getRate();

    public function getNumber();

} 