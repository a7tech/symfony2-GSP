<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 7/18/13
 * Time: 12:45 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\PersonBundle\DataFixtures\ORM;

use App\AddressBundle\Entity\Address;
use App\EmailBundle\Entity\Email;
use App\PersonBundle\Entity\Person;
use App\PersonBundle\Entity\PersonGroup;
use App\PhoneBundle\Entity\Phone;
use App\PhoneBundle\Entity\PhoneType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadClientsData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        $row = 1;
        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'clients.csv';

        $group = $manager->getRepository('AppPersonBundle:PersonGroup')->findOneByName('Clients');
        if (empty($group)) {
            $group = new PersonGroup();
            $group->setName('Clients');
            $manager->persist($group);
        }
        $country = $manager->getRepository('AppAddressBundle:Country')->findOneByName('Canada');
        $phone_work_t = $manager->getRepository('AppPhoneBundle:PhoneType')->findOneByName('Work');
        if (empty($phone_work_t)) {
            $phone_work_t = new PhoneType();
            $phone_work_t->setName('Work');
            $manager->persist($phone_work_t);
        }
        $phone_home_t = $manager->getRepository('AppPhoneBundle:PhoneType')->findOneByName('Home');
        if (empty($phone_home_t)) {
            $phone_home_t = new PhoneType();
            $phone_home_t->setName('Home');
            $manager->persist($phone_home_t);
        }
        $phone_mobile_t = $manager->getRepository('AppPhoneBundle:PhoneType')->findOneByName('Mobile');
        if (empty($phone_mobile_t)) {
            $phone_mobile_t = new PhoneType();
            $phone_mobile_t->setName('Mobile');
            $manager->persist($phone_mobile_t);
        }
        $phone_other_t = $manager->getRepository('AppPhoneBundle:PhoneType')->findOneByName('Other');
        if (empty($phone_other_t)) {
            $phone_other_t = new PhoneType();
            $phone_other_t->setName('Other');
            $manager->persist($phone_other_t);
        }
        
        $phone_iso = $manager->getRepository('AppPhoneBundle:PhoneIso')->findCanada();


        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {


                $person = new Person();
                $person->setPersonGroup(array($group));
                $person->setFirstName($data[1]);
                $person->setLastName($data[3]);

                $email_arr = array();
                if (!empty($data[15])) {
                    $email = new Email();
                    $email->setEmail($data[15]);
                    $manager->persist($email);
                    $person->addEmail($email);

                }
                if (!empty($data[16])) {
                    $email2 = new Email();
                    $email2->setEmail($data[16]);
                    $manager->persist($email2);
                    $person->addEmail($email2);
                }
                if (!empty($data[17])) {
                    $email3 = new Email();
                    $email3->setEmail($data[17]);
                    $manager->persist($email3);
                    $person->addEmail($email3);
                }


                $phone_arr = array();
                if (!empty($data[18])) {
                    $work_phone = new Phone();
                    $work_phone->setNumber($data[18]);
                    $work_phone->setExtension($data[19]);
                    $work_phone->setPhoneIsoCode($phone_iso[0]);
                    $work_phone->setPhoneType($phone_work_t);
                    $manager->persist($work_phone);
                    $phone_arr[] = $work_phone;
                }
                if (!empty($data[20])) {
                    $home_phone = new Phone();
                    $home_phone->setNumber($data[20]);
                    $home_phone->setPhoneIsoCode($phone_iso[0]);
                    $home_phone->setPhoneType($phone_home_t);
                    $manager->persist($home_phone);
                    $phone_arr[] = $home_phone;
                }

                if (!empty($data[21])) {
                    $mobile_phone = new Phone();
                    $mobile_phone->setNumber($data[21]);
                    $mobile_phone->setPhoneIsoCode($phone_iso[0]);
                    $mobile_phone->setPhoneType($phone_mobile_t);
                    $manager->persist($mobile_phone);
                    $phone_arr[] = $mobile_phone;
                }

                if (!empty($data[22])) {
                    $other_phone = new Phone();
                    $other_phone->setNumber($data[22]);
                    $other_phone->setPhoneIsoCode($phone_iso[0]);
                    $other_phone->setPhoneType($phone_other_t);
                    $manager->persist($other_phone);
                    $phone_arr[] = $other_phone;
                }

                if (!empty($phone_arr)){
                    $person->setPhones($phone_arr);
                }

                if (!empty($data[25])) {
                    $address = new Address();
                    $spl = explode(', ', $data[25]);
                    $address->setBuilding($spl[0]);
                    $address->setStreet($spl[1]);
                    $address->setSuite($data[26]);
                    $address->setCity($data[28]);
                    $address->setPostcode($data[30]);
                    
                    $address->setCountry($country);
                    $manager->persist($address);
                    $person->setAddresses(array($address));
                }


                if ($person) {
                    $manager->persist($person);
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