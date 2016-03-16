<?php

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use TaskManagerBundle\Entity\Task;

class CreateNewTaskFeatureTest extends WebTestCase
{
    protected $client;

    const TASK_TABLE_ROW = 'table > tbody > tr';

    protected function setUp()
    {
        $this->loadFixtures(array());
        $this->client = static::makeClient();
    }

    /**
     * @test
     */
    public function should_return_ok_when_access_to_new_task_page()
    {
        $this->requestNewTaskPage();
        $this->isSuccessful($this->client->getResponse());
    }

    /**
     * @test
     */
    public function should_show_a_form_to_create_a_new_task()
    {
        $this->assertEquals(1, $this->getNewTaskForm()->count());
    }

    /**
     * @test
     */
    public function should_show_required_label_for_required_fields()
    {
        $newTaskForm = $this->getNewTaskForm();
        $this->assertEquals('required', $newTaskForm->filter('label[for="task_name"]')->attr('class'));
        $this->assertEquals('required', $newTaskForm->filter('label')->eq(2)->attr('class'));
        $this->assertEquals('required', $newTaskForm->filter('label[for="task_priority"]')->attr('class'));
    }

    /**
     * @test
     */
    public function should_not_show_required_label_for_not_required_fields()
    {
        $newTaskForm = $this->getNewTaskForm();
        $this->assertEquals('', $newTaskForm->filter('label[for="task_description"]')->attr('class'));
        $this->assertEquals('', $newTaskForm->filter('label[for="task_category"]')->attr('class'));
    }

    /**
     * @test
     */
    public function should_show_uncatalogued_option_as_default_category()
    {
        $newTaskForm = $this->getNewTaskForm();
        $this->assertEquals('(Uncatalogued)', $newTaskForm->filter('select#task_category > option')->text());
        $this->assertEquals('', $newTaskForm->filter('select#task_category > option')->attr('value'));
    }

    /**
     * @test
     */
    public function should_show_normal_option_as_default_priority()
    {
        $newTaskForm = $this->getNewTaskForm();
        $this->assertEquals('Normal', $newTaskForm->filter('select#task_priority > option[selected]')->text());
        $this->assertEquals('Normal', $newTaskForm->filter('select#task_priority > option[selected]')->attr('value'));
    }

    /**
     *
     * @test
     *
     */
    public function should_create_a_task_and_redirect_to_list_page()
    {
        $task = new Task();
        $task->setName("New Task");
        $task->setCategory("Work");
        $task->setDescription("This task will be created");
        $task->setDueDate(new \DateTime('2012-12-25 15:30'));
        $task->setPriority("Normal");

        $newTaskForm = $this->requestNewTaskPage()
            ->selectButton("Create")
            ->form(array(
                'task[name]' => $task->getName(),
                'task[description]' => $task->getDescription(),
                'task[category]' => $task->getCategory(),
                'task[dueDate]' => '2012-12-25 15:30'
            ));

        $this->client->submit($newTaskForm);
        $this->client->followRedirect();
        $this->isSuccessful($this->client->getResponse());

        $firstTaskName = $this->getFirstTaskDataColumn()->first()->text();
        $this->assertEquals($task->getName(), $firstTaskName);

        $firstTaskDescription = $this->getFirstTaskDataColumn()->eq(1)->text();
        $this->assertEquals($task->getDescription(), $firstTaskDescription);

        $firstTaskDueDateString = $this->getFirstTaskDataColumn()->eq(2)->text();
        $this->assertEquals('2012-12-25 03:30 PM', $firstTaskDueDateString);
    }

    /**
     * @test
     */
    public function should_not_insert_task_when_cancel_is_clicked()
    {
        $cancelLink = $this->requestNewTaskPage()
            ->selectLink("Cancel")
            ->link();
        $this->client->click($cancelLink);
        $this->isSuccessful($this->client->getResponse());
        $this->assertEquals(0, $this->getTableRow()->count());
    }


    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function requestNewTaskPage()
    {
        return $this->client->request('GET', '/tasks/new');
    }

    /**
     * @return mixed
     */
    private function getFirstTaskDataColumn()
    {
        return $this->getTableRow()->first()->filter('td');
    }

    /**
     * @return mixed
     */
    private function getTableRow()
    {
        return $this->client->getCrawler()->filter(self::TASK_TABLE_ROW);
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function getNewTaskForm()
    {
        return $this->requestNewTaskPage()->filter('form[name="task"]');
    }

}