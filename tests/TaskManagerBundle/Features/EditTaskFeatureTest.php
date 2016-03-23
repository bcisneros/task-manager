<?php
/**
 * Created by PhpStorm.
 * User: cisneben
 * Date: 15/03/2016
 * Time: 12:52 PM
 */

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData;
use TaskManagerBundle\Entity\Task;


class EditTaskFeatureTest extends WebTestCase
{

    protected $client;

    const TASK_LIST_ROUTE = 'en/tasks/';

    protected function setUp()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
            'TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
        $this->client = static::makeClient();
    }

    /**
     * @test
     */
    public function should_return_ok_when_access_to_edit_task_page()
    {
        $this->clickOnEditLink();
        $this->isSuccessful($this->client->getResponse());
    }

    /**
     * @test
     */
    public function should_show_a_title_with_task_name()
    {
        $this->clickOnEditLink();
        $editTaskTitle = $this->client->getCrawler()->filter('h2')->text();
        $this->assertEquals('Edit task "' . LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME . '"', $editTaskTitle);
    }

    /**
     * @test
     */
    public function should_show_task_data_in_form_values()
    {
        $this->clickOnEditLink();
        $this->validateFirstTaskData();
    }

    /**
     * @test
     */
    public function should_update_task_values()
    {
        $task = new Task();
        $task->setName("Other name");
        $task->setCategory("Social");
        $task->setDescription("Other description");
        $task->setDueDate(new \DateTime('2012-12-25 15:30'));
        $task->setPriority("Low");

        $this->clickOnEditLink();

        $editTaskForm = $this->client->getCrawler()->selectButton('Save')->form(array(
            'task[name]' => $task->getName(),
            'task[description]' => $task->getDescription(),
            'task[category]' => $task->getCategory(),
            'task[dueDate]' => '2012-12-25 15:30',
            'task[priority]' => $task->getPriority()
        ));
        $this->client->submit($editTaskForm);
        $this->client->followRedirect();
        $this->isSuccessful($this->client->getResponse());

        $firstTaskName = $this->getFirstRowData()->first()->text();
        $this->assertEquals($task->getName(), $firstTaskName);

        $firstTaskDescription = $this->getFirstRowData()->eq(1)->text();
        $this->assertEquals($task->getDescription(), $firstTaskDescription);

        $firstTaskDueDateString = $this->getFirstRowData()->eq(2)->text();
        $this->assertEquals('2012-12-25 03:30 PM', $firstTaskDueDateString);
    }

    /**
     * @test
     */

    public function should_back_to_list_when_cancel_button_is_clicked()
    {
        $this->clickOnEditLink();
        $cancelLink = $this->client->getCrawler()
            ->selectLink("Cancel")
            ->link();
        $this->client->click($cancelLink);
        $this->isSuccessful($this->client->getResponse());
        $firstTaskName = $this->client->getCrawler()->filter('table > tbody > tr')->first()->filter('td')->first()->text();
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME, $firstTaskName);
    }

    private function clickOnEditLink()
    {
        $editLink = $this->client->request('GET', self::TASK_LIST_ROUTE)->selectLink('Edit')->first()->link();
        $this->client->click($editLink);
    }

    /**
     * @return mixed
     */
    private function getFirstRowData()
    {
        return $this->client->getCrawler()->filter('table > tbody > tr')->first()->filter('td');
    }

    private function validateFirstTaskData()
    {
        $taskName = $this->client->getCrawler()->filter('form[name="task"] input#task_name')->attr('value');
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME, $taskName);
        $taskDescription = $this->client->getCrawler()->filter('form[name="task"] textarea#task_description')->text();
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_DESCRIPTION, $taskDescription);

        $taskDueDate = $this->client->getCrawler()->filter('form[name="task"] input#task_dueDate')->attr('value');
        $this->assertEquals('2016-01-01 00:00', $taskDueDate);

        $taskCategory = $this->client->getCrawler()->filter('form[name="task"] select#task_category > option[selected]')->attr('value');
        $this->assertEquals("Work", $taskCategory);

        $taskPriority = $this->client->getCrawler()->filter('form[name="task"] select#task_priority > option[selected]')->attr('value');
        $this->assertEquals("Urgent", $taskPriority);
    }

    /**
     * Use this function when you want to debug and see on the html in the browser
     * Browse http://localhost:8000/debug.html or whatever name you provide
     * @param string $name
     */

    protected function createDebugFile($name = 'debug.html')
    {
        $debugFile = fopen("../../../web/debug/$name", "w") or die("Unable to open file!");
        fwrite($debugFile, $this->client->getResponse()->getContent());
        fclose($debugFile);
    }


}