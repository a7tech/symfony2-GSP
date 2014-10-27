<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/11/13
 * Time: 8:02 AM
 * To change this template use File | Settings | File Templates.
 */
namespace App\AddressBundle\DataFixtures\ORM;

use App\AddressBundle\Entity\Province;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadProvinceData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'country_state_province_list.csv';

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $province = new Province();
                $province->setName($data[2]);
                $province->setAlterName($data[4]);
                $province->setIsoCode($data[1]);
                $province->setLevelName($data[3]);
                $province->setCdhId($data[5]);

                $country = $manager->getRepository('AppAddressBundle:Country')->findOneByCdhId($data[6]);
                $province->setCountry($country);

                $manager->persist($province);
            }
            fclose($handle);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }
}