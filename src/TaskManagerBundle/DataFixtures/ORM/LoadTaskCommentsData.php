<?php

namespace TaskManagerBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use TaskManagerBundle\Entity\Comment;
use TaskManagerBundle\Entity\Task;

class LoadTaskCommentsData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $taskWithComment = new Task();
        $taskWithComment->setName("Task with comment");
        $taskWithComment->setDueDate(new \DateTime());
        $taskWithComment->setStatus("New");

        $taskWithoutComments = new Task();
        $taskWithoutComments->setName("In progress task");
        $taskWithoutComments->setDueDate(new \DateTime());
        $taskWithoutComments->setStatus("In progress");

        $manager->persist($taskWithComment);
        $manager->persist($taskWithoutComments);

        $comment = new Comment();
        $comment->setTask($taskWithComment);
        $comment->setComment('A comment');
        $manager->persist($comment);

        $manager->flush();
    }
}