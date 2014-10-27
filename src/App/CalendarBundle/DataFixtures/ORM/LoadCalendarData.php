<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 6/11/13
 * Time: 8:02 AM
 * To change this template use File | Settings | File Templates.
 */
namespace App\CalendarBundle\DataFixtures\ORM;

use App\CalendarBundle\Entity\EventEntity;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadCalendarData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {

            $curr = new EventEntity('Testing event for calendar', new \DateTime(), new \DateTime(), true);

            $manager->persist($curr);

            $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}