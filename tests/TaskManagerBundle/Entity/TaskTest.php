<?php
/**
 * Created by PhpStorm.
 * User: cisneben
 * Date: 14/03/2016
 * Time: 05:22 PM
 */

namespace TaskManagerBundle\Entity;


use Liip\FunctionalTestBundle\Test\WebTestCase;


class TaskTest extends WebTestCase
{

    /**
     * @test
     */
    public function should_change_to_in_progress_when_task_is_new()
    {
        $task = new Task();
        $task->setStatus("New");
        $task->changeStatus();
        $this->assertEquals("In progress", $task->getStatus());
    }

    /**
     * @test
     */
    public function should_change_to_closed_when_task_is_in_progress()
    {
        $task = new Task();
        $task->setStatus("In progress");
        $task->changeStatus();
        $this->assertEquals("Closed", $task->getStatus());
    }
}
