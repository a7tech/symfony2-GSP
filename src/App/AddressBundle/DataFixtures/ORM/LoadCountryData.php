<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/11/13
 * Time: 8:02 AM
 * To change this template use File | Settings | File Templates.
 */
namespace App\AddressBundle\DataFixtures\ORM;

use App\AddressBundle\Entity\Country;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadCountryData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        $row = 1;
        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'country_list.csv';

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                $country = new Country();
                $country->setName( $data[0]);
                $country->setAlterName($data[1]);
                $country->setTwoCharCode($data[2]);
                $country->setThreeCharCode($data[3]);
                $country->setNumberCode($data[4]);
                $country->setFipsCountryCode($data[5]);
                $country->setFipsCountryName($data[6]);
                $country->setCdhId($data[9]);
                $country->setLat($data[10]);
                $country->setLong($data[11]);

                if ($country) {
                    $manager->persist($country);
                }
            }
            fclose($handle);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}