<?php

namespace TaskManagerBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use TaskManagerBundle\Entity\Task;

class LoadInitialTaskData implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sendEmailTask = new Task();
        $sendEmailTask->setName("Send email");
        $sendEmailTask->setDescription("Send sales report to manager@company.com");
        $sendEmailTask->setDueDate(new \DateTime('15-01-2016 04:00'));
        $sendEmailTask->setCreationDate(new \DateTime('14-01-2016 02:45'));
        $sendEmailTask->setCategory("Work");
        $sendEmailTask->setPriority("Normal");
        $sendEmailTask->setStatus("New");

        $goToMarketTask = new Task();
        $goToMarketTask->setName("Go to Market");
        $goToMarketTask->setDescription("Buy carrots and lettuce for dinner");
        $goToMarketTask->setDueDate(new \DateTime('23-01-2016 19:00'));
        $goToMarketTask->setCreationDate(new \DateTime('20-01-2016 04:16'));
        $goToMarketTask->setCategory("Home");
        $goToMarketTask->setPriority("Low");
        $goToMarketTask->setStatus("New");

        $pickMySonTask = new Task();
        $pickMySonTask->setName("Pick my son");
        $pickMySonTask->setDescription("Go to school and pick my son");
        $pickMySonTask->setDueDate(new \DateTime('25-01-2016 02:30'));
        $pickMySonTask->setCreationDate(new \DateTime('25-01-2016 02:16'));
        $pickMySonTask->setCategory("Personal");
        $pickMySonTask->setPriority("Urgent");
        $pickMySonTask->setStatus("Open");

        $manager->persist($sendEmailTask);
        $manager->persist($goToMarketTask);
        $manager->persist($pickMySonTask);
        $manager->flush();
    }
}