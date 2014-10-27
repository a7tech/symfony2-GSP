<?php
namespace App\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\UserBundle\Entity\Role;
use App\UserBundle\Entity\Permissions;
use App\UserBundle\Entity\PermissionsGroup;
use App\PersonBundle\Entity\Person;

class LoadPermissionData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // These entities are used to create a list that can be used in a crud interface for adding roles to a group
        $role_super_admin = new Role('ROLE_SUPER_ADMIN');
        $manager->persist($role_super_admin);

        $role_admin = new Role('ROLE_ADMIN');
        $manager->persist($role_admin);

        $role_switch = new Role('ROLE_ALLOWED_TO_SWITCH');
        $manager->persist($role_switch);

        $role_backend = new Role('ROLE_BACKEND_ALL_SETTINGS');
        $manager->persist($role_backend);

        $role_backenddb = new Role('ROLE_BACKEND_DASHBOARD');
        $manager->persist($role_backenddb);

        $group = new PermissionsGroup('SuperAdmin');
        $group->addRole($role_backend);
        $group->addRole($role_super_admin);
        $group->addRole($role_admin);
        $group->addRole($role_switch);

        $manager->persist($group);

        $person = new Person();
        $person->setFirstName('Admin');
        $person->setLastName('test');
        $manager->persist($person);

        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('admin@example.com');
        $user->setEmail('admin@example.com');
        $user->setPlainPassword('admin');
        $user->setPerson($person);
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $user->addGroup($group);

        $role_calendar_show = new Role('ROLE_BACKEND_CALENDAR_SHOW');
        $manager->persist($role_calendar_show);

        $permgroup = new PermissionsGroup('Calendar');
        $permgroup->addRole($role_calendar_show);
        $permgroup->addRole($role_backenddb); // To make sure it can access /backend urls
        
        $manager->persist($permgroup);

        $person = new Person();
        $person->setFirstName('Calendar');
        $person->setLastName('test');
        $manager->persist($person);

        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('calendar@example.com');
        $user->setEmail('calendar@example.com');
        $user->setPlainPassword('test');
        $user->setPerson($person);
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $user->addGroup($permgroup);

        $manager->flush();
    }
}