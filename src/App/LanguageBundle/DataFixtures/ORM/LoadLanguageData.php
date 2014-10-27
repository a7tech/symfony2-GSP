<?php
namespace App\LanguageBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use App\LanguageBundle\Entity\Language;

class LoadLanguageData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $row = 1;
        $filePath = getcwd().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR.'languages.csv';

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {


                $lang = new Language();
                $lang->setName($data[0]);
                $lang->setIso(strtolower($data[1]));

                if ($data[1]=='EN') {
                    $lang->setBackend(1);
                    $lang->setFrontend(1);
                }
                else {
                    $lang->setBackend(0);
                    $lang->setFrontend(0);
                }

                $manager->persist($lang);

                $manager->flush();
            }
        }
    }
}