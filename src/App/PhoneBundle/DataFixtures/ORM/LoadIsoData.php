<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/12/13
 * Time: 11:03 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\PhoneBundle\DataFixtures\ORM;

use App\PhoneBundle\Entity\PhoneIso;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIsoData extends AbstractFixture {


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'iso_phone.csv';

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $iso = new PhoneIso();
                $iso->setPrefix($data[5]);
                $country = $manager->getRepository('AppAddressBundle:Country')->findOneByName($data[1]);
                //var_dump($country);
                $iso->setCountry($country);

                $manager->persist($iso);
            }
            fclose($handle);
        }
        $manager->flush();
    }
}