<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-05-14
 * Time: 22:24
 */

namespace App\CoreBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration as BaseAbstractMigration;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractMigration extends BaseAbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /* (non-PHPdoc)
     * @see \Symfony\Component\DependencyInjection\ContainerAwareInterface::setContainer()
    */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function loadDataFixtures(array $fixtures)
    {
        $loader = new Loader();
        foreach($fixtures as $fixture){
            if($fixture instanceof ContainerAwareInterface){
                $fixture->setContainer($this->container);
            }

            $loader->addFixture($fixture);
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures(), true);
    }

    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }
}