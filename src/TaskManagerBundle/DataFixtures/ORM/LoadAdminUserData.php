<?php

namespace TaskManagerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TaskManagerBundle\Entity\User;

class LoadAdminUserData extends AbstractFixture implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword('admin');
        $admin->setPlainPassword('admin');
        $admin->setEmail('admin@test.com');
        $admin->setEnabled(true);
        $admin->setSuperAdmin(true);
        $manager->persist($admin);
        $this->addReference('admin', $admin);
        $manager->flush();
    }
}