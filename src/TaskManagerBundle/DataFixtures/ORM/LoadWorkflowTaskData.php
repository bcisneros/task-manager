<?php

namespace TaskManagerBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use TaskManagerBundle\Entity\Comment;
use TaskManagerBundle\Entity\Task;
use TaskManagerBundle\Entity\User;

class LoadWorkFlowTaskData extends AbstractFixture implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $newStatusTask = new Task();
        $newStatusTask->setName("New Task");
        $newStatusTask->setDueDate(new \DateTime());
        $newStatusTask->setStatus("New");

        $inProgressTask = new Task();
        $inProgressTask->setName("In progress task");
        $inProgressTask->setDueDate(new \DateTime());
        $inProgressTask->setStatus("In progress");

        $closedTask = new Task();
        $closedTask->setName("Closed task");
        $closedTask->setDueDate(new \DateTime());
        $closedTask->setStatus("Closed");

        $manager->persist($newStatusTask);
        $manager->persist($inProgressTask);
        $manager->persist($closedTask);

        $manager->flush();
    }
}