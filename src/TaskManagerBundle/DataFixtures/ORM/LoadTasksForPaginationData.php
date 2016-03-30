<?php

namespace TaskManagerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TaskManagerBundle\Entity\Task;

class LoadTasksForPaginationData extends AbstractFixture implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $loggedInUser = $this->getReference("admin");
        for ($i = 1; $i <= 95; $i++) {
            $task = new Task();
            $task->setName('Task ' . $i);
            $task->setDueDate(new \DateTime());
            $task->setUser($loggedInUser);
            $manager->persist($task);
        }

        $manager->flush();
    }
}