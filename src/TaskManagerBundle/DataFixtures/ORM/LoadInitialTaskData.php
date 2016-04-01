<?php

namespace TaskManagerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TaskManagerBundle\Entity\Comment;
use TaskManagerBundle\Entity\Task;

class LoadInitialTaskData extends AbstractFixture implements FixtureInterface
{
    const OLDEST_DUE_DATE_TASK_NAME = "Oldest Due Date Task";
    const OLDEST_DUE_DATE_TASK_DESCRIPTION = "This task should appear first because have the earlier due date";

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $loggedInUser = $this->getReference("admin");

        $sendEmailTask = new Task();
        $sendEmailTask->setName("Send email");
        $sendEmailTask->setDescription("Send sales report to manager@company.com.\nCall Bob tomorrow");
        $sendEmailTask->setDueDate(new \DateTime('15-01-2016 16:00'));
        $sendEmailTask->setCreationDate(new \DateTime('14-01-2016 02:45'));
        $sendEmailTask->setCategory("Work");
        $sendEmailTask->setPriority("Normal");
        $sendEmailTask->setStatus("New");
        $sendEmailTask->setUser($loggedInUser);

        $goToMarketTask = new Task();
        $goToMarketTask->setName("Go to Market");
        $goToMarketTask->setDescription("Buy carrots and lettuce for dinner");
        $goToMarketTask->setDueDate(new \DateTime('23-01-2016 19:00'));
        $goToMarketTask->setCreationDate(new \DateTime('20-01-2016 04:16'));
        $goToMarketTask->setCategory("Home");
        $goToMarketTask->setPriority("Low");
        $goToMarketTask->setStatus("New");
        $goToMarketTask->setUser($loggedInUser);

        $pickMySonTask = new Task();
        $pickMySonTask->setName("Pick my son");
        $pickMySonTask->setDescription("Go to school and pick my son");
        $pickMySonTask->setDueDate(new \DateTime('+1week'));
        $pickMySonTask->setCreationDate(new \DateTime('25-01-2016 02:16'));
        $pickMySonTask->setCategory("Personal");
        $pickMySonTask->setPriority("Urgent");
        $pickMySonTask->setStatus("In progress");
        $pickMySonTask->setUser($loggedInUser);

        $oldestDueDateTask = new Task();
        $oldestDueDateTask->setName(self::OLDEST_DUE_DATE_TASK_NAME);
        $oldestDueDateTask->setDescription(self::OLDEST_DUE_DATE_TASK_DESCRIPTION);
        $oldestDueDateTask->setDueDate(new \DateTime('01-01-2016 00:00'));
        $oldestDueDateTask->setCreationDate(new \DateTime('25-12-2015 02:16'));
        $oldestDueDateTask->setCategory("Work");
        $oldestDueDateTask->setPriority("Urgent");
        $oldestDueDateTask->setStatus("In progress");
        $oldestDueDateTask->setUser($loggedInUser);

        $closedTask = new Task();
        $closedTask->setName("Closed Task");
        $closedTask->setDescription("This task is closed.");
        $closedTask->setDueDate(new \DateTime('25-01-2016 14:30'));
        $closedTask->setCreationDate(new \DateTime('25-01-2016 02:16'));
        $closedTask->setCategory("Personal");
        $closedTask->setPriority("Urgent");
        $closedTask->setStatus("Closed");
        $closedTask->setUser($loggedInUser);

        $anotherUserTask = new Task();
        $anotherUserTask->setName("Another user task");
        $anotherUserTask->setDescription("Another user task");
        $anotherUserTask->setDueDate(new \DateTime('25-01-2016 14:30'));
        $anotherUserTask->setCreationDate(new \DateTime('25-01-2016 02:16'));
        $anotherUserTask->setCategory("Personal");
        $anotherUserTask->setPriority("Urgent");
        $anotherUserTask->setStatus("In progress");
        $anotherUserTask->setUser(null);

        $manager->persist($sendEmailTask);
        $manager->persist($goToMarketTask);
        $manager->persist($pickMySonTask);
        $manager->persist($oldestDueDateTask);
        $manager->persist($closedTask);
        $manager->persist($anotherUserTask);

        $comment = new Comment();
        $comment->setComment("This is a test comment");
        $comment->setCreationDate(new \DateTime('23-01-2016'));
        $comment->setTask($oldestDueDateTask);
        $manager->persist($comment);

        $manager->flush();
    }
}