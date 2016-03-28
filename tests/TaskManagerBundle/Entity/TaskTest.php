<?php

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

    /**
     * @test
     */
    public function should_return_true_when_a_task_is_overdue()
    {
        $task = new Task();
        $task->setDueDate(new \DateTime('-3weeks'));
        $this->assertTrue($task->overdue());
    }

    /**
     * @test
     */
    public function should_return_false_when_a_task_is_not_overdue()
    {
        $task = new Task();
        $task->setDueDate(new \DateTime('+3weeks'));
        $this->assertFalse($task->overdue());
    }
}
