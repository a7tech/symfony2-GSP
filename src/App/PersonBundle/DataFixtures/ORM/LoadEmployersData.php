<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 7/18/13
 * Time: 8:31 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\PersonBundle\DataFixtures\ORM;


use App\AccountBundle\Entity\AccountProfile;
use App\AddressBundle\Entity\Address;
use App\CompanyBundle\Entity\Company;
use App\EmailBundle\Entity\Email;
use App\EmploymentBundle\Entity\Employment;
use App\PersonBundle\Entity\Person;
use App\PersonBundle\Entity\PersonGroup;
use App\PhoneBundle\Entity\Phone;
use App\PhoneBundle\Entity\PhoneType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadEmployersData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

        $row = 1;
        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'employers.csv';


        $group = $manager->getRepository('AppPersonBundle:PersonGroup')->findOneByName('Employers');
        if (empty($group)) {
            $group = new PersonGroup();
            $group->setName('Employers');
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
        $phone_fax_t = $manager->getRepository('AppPhoneBundle:PhoneType')->findOneByName('Fax');
        if (empty($phone_fax_t)) {
            $phone_fax_t = new PhoneType();
            $phone_fax_t->setName('Fax');
            $manager->persist($phone_fax_t);
        }

        $phone_iso = $manager->getRepository('AppPhoneBundle:PhoneIso')->findCanada();

        $accountProfile = $manager->getRepository('AppAccountBundle:AccountProfile')->findOneByName('GSP - Reno Urbaine');
        if(empty($accountProfile)) {
            $accountProfile = new AccountProfile();

            $accountProfile->setName('GSP - Reno Urbaine');

            $accAdd = new Address();
            $accAdd->setCountry($country);
            $accAdd->setPostcode('H1W 2C1');
            $accAdd->setCity('Montreal');
            $accAdd->setStreet('Sainte-Catherine Est');
            $accAdd->setBuilding('3115');
            $manager->persist($accAdd);

            $accountProfile->addAddress($accAdd);

            $fax = new Phone();
            $fax->setPhoneIsoCode($phone_iso[0]);
            $fax->setNumber('514-524-5173');
            $manager->persist($fax);
            $accountProfile->addPhone($fax);
            $bus = new Phone();
            $bus->setPhoneIsoCode($phone_iso[0]);
            $bus->setNumber('514-524-2362');
            $manager->persist($bus);

            $accountProfile->addPhone($bus);
            $manager->persist($accountProfile);
        }

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, "\t");
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {


                $person = new Person();
                $person->setPersonGroup(array($group));
                $person->setFirstName($data[0]);
                $person->setLastName($data[1]);


                if (!empty($data[3])) {
                    $email = new Email();
                    $email->setEmail($data[3]);
                    $manager->persist($email);
                    $person->addEmail($email);

                }
                if (!empty($data[2])) {
                    $email2 = new Email();
                    $email2->setEmail($data[2]);
                    $manager->persist($email2);
                    $person->addEmail($email2);
                }



                $phone_arr = array();
                if (!empty($data[14])) {
                    $work_phone = new Phone();
                    $work_phone->setNumber($data[14]);
                    $work_phone->setExtension($data[15]);
                    $work_phone->setPhoneIsoCode($phone_iso[0]);
                    $work_phone->setPhoneType($phone_work_t);
                    $manager->persist($work_phone);
                    $phone_arr[] = $work_phone;
                }
                if (!empty($data[11])) {
                    $home_phone = new Phone();
                    $home_phone->setNumber($data[11]);
                    $home_phone->setPhoneIsoCode($phone_iso[0]);
                    $home_phone->setPhoneType($phone_home_t);
                    $manager->persist($home_phone);
                    $phone_arr[] = $home_phone;
                }

                if (!empty($data[13])) {
                    $mobile_phone = new Phone();
                    $mobile_phone->setNumber($data[13]);
                    $mobile_phone->setPhoneIsoCode($phone_iso[0]);
                    $mobile_phone->setPhoneType($phone_mobile_t);
                    $manager->persist($mobile_phone);
                    $phone_arr[] = $mobile_phone;
                }

                if (!empty($data[12])) {
                    $other_phone = new Phone();
                    $other_phone->setNumber($data[12]);
                    $other_phone->setPhoneIsoCode($phone_iso[0]);
                    $other_phone->setPhoneType($phone_other_t);
                    $manager->persist($other_phone);
                    $phone_arr[] = $other_phone;
                }

                if (!empty($phone_arr)){
                    $person->setPhones($phone_arr);
                }

                if (!empty($data[6])) {
                    $address = new Address();
                    $spl = explode(', ', $data[6]);
                    $address->setBuilding($spl[0]);
                    $address->setStreet($spl[1]);
                    $address->setSuite($data[7]);
                    $address->setCity($data[8]);
                    $address->setPostcode($data[10]);

                    $address->setCountry($country);
                    $manager->persist($address);
                    $person->setAddresses(array($address));
                }


                if ($person) {
                    $manager->persist($person);
                }
                if (!empty($data[4])) {

                    $person->setBirthDate(new \DateTime($data[4]));
                }
                if ($data[5]=='man') {
                    $person->setGender('m');
                }
                elseif ($data[5]=='woman') {
                    $person->setGender('f');
                }

                $manager->persist($person);

                $empl = new Employment();
                $empl->setCompany($accountProfile);
                $empl->setPerson($person);
                $manager->persist($empl);
            }
            fclose($handle);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}