<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 7/23/13
 * Time: 10:15 PM
 * To change this template use File | Settings | File Templates.
 */
namespace App\CurrencyBundle\DataFixtures\ORM;


use App\CurrencyBundle\Entity\Currency;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCurrenciesData extends AbstractFixture {

    public function load(ObjectManager $manager) {

        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'currencies.csv';

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $curr = new Currency();

                $curr->setName($data[2]);
                $curr->setCode($data[1]);
                $curr->setSymbol($data[3]);

                $manager->persist($curr);
            }
            $manager->flush();
        }

    }
}