<?php

namespace TaskManagerBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use TaskManagerBundle\Entity\Comment;
use TaskManagerBundle\Entity\Task;
use TaskManagerBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;

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