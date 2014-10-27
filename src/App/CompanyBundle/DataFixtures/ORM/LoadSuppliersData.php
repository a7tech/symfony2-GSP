<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 7/18/13
 * Time: 10:21 AM
 * To change this template use File | Settings | File Templates.
 */
namespace App\CompanyBundle\DataFixtures\ORM;

use App\AddressBundle\Entity\Address;
use App\CompanyBundle\Entity\Company;
use App\PhoneBundle\Entity\Phone;
use App\PhoneBundle\Entity\PhoneType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;



class LoadSuppliersData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {
        $phone_work_t = $manager->getRepository('AppPhoneBundle:PhoneType')->findOneByName('Business');
        if (empty($phone_work_t)) {
            $phone_work_t = new PhoneType();
            $phone_work_t->setName('Business');
            $manager->persist($phone_work_t);
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
        $row = 1;
        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'suppliers.csv';

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                $company = new Company();
                $company->setName($data[0]);

                $address = new Address();
                if (!empty($data[2])) {
                    $spl = explode(', ', $data[2]);
                    $address->setBuilding($spl[0]);
                    $address->setStreet($spl[1]);
                }
                $address->setPo($data[3]);
                $address->setSuite($data[4]);
                $address->setCity($data[5]);
                $province = $manager->getRepository('AppAddressBundle:Province')->findOneByName($data[6]);
                $address->setProvince($province);
                $address->setPostcode($data[7]);
                $country = $manager->getRepository('AppAddressBundle:Country')->findOneByName('Canada');

                $address->setCountry($country);
                $manager->persist($address);
                $company->addAddress($address);

                if ($country) {
                    $manager->persist($country);
                }


                if (!empty($data[8])) {
                    $phone = new Phone();
                    $phone->setPhoneType($phone_work_t);
                    $phone_iso = $manager->getRepository('AppPhoneBundle:PhoneIso')->findCanada();
                    $phone->setPhoneIsoCode($phone_iso[0]);
                    $phone->setNumber($data[8]);
                    $phone->setExtension($data[9]);
                    $manager->persist($phone);

                    $company->addPhone($phone);
                }
                if (!empty($data[10])) {
                    $phone1 = new Phone();
                    $phone1->setPhoneType($phone_other_t);

                    $phone1->setPhoneIsoCode($phone_iso[0]);
                    $phone1->setNumber($data[10]);
                    $manager->persist($phone1);

                    $company->addPhone($phone1);
                }

                if (!empty($data[12])) {
                    $phone2 = new Phone();
                    $phone2->setPhoneType($phone_fax_t);

                    $phone2->setPhoneIsoCode($phone_iso[0]);
                    $phone2->setNumber($data[12]);
                    $manager->persist($phone2);

                    $company->addPhone($phone2);
                }
                $manager->persist($company);
            }
            fclose($handle);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}