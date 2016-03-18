<?php

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use TaskManagerBundle\Entity\Task;

class CreateNewTaskFeatureTest extends WebTestCase
{
    protected $client;

    const TASK_TABLE_ROW = 'table > tbody > tr';

    const TASK_DESCRIPTION_LARGER_THAN_255_CHARACTERS = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id aliquet mi, quis facilisis sapien. Nulla eget consequat nulla. Aenean tincidunt velit mauris, et convallis elit tempus a. Morbi lectus ipsum, tincidunt at efficitur in, finibus ac odio. Proin eu arcu in felis tincidunt viverra. Ut eu libero ut dui pellentesque laoreet. Integer suscipit tortor et aliquet viverra. Sed faucibus nec dolor a vehicula. Morbi vitae lectus porta, ullamcorper libero quis, efficitur tortor.';

    const TASK_DESCRIPTION_EQUAL_TO_255_CHARACTERS = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ut luctus ex. Phasellus purus diam, molestie quis arcu nec, interdum lobortis lectus. Duis quis orci sit amet urna vulputate luctus. In pulvinar purus vitae purus ornare, ac congue erat metus.";

    protected function setUp()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
        $this->client = static::makeClient(true);
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
    public function should_create_a_task_and_assign_to_the_current_user_and_redirect_to_list_page()
    {
        $task = $this->createTask("This task will be created");
        $this->validateTaskIsCreated($task);
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
     * @test
     */

    public function should_not_throw_500_error_when_create_task_with_description_greater_than_255_characters()
    {
        $this->createTask(self::TASK_DESCRIPTION_LARGER_THAN_255_CHARACTERS);
        $this->assertValidationErrors(array('data.description'), $this->client->getContainer());
        $this->isSuccessful($this->client->getResponse());
    }

    /**
     *
     * @test
     *
     */
    public function should_create_a_task_for_description_equals_to_255_characters()
    {
        $task = $this->createTask(self::TASK_DESCRIPTION_EQUAL_TO_255_CHARACTERS);
        $this->validateTaskIsCreated($task);
    }

    /**
     *
     * @test
     *
     */
    public function should_create_a_task_for_description_less_than_255_characters()
    {
        $task = $this->createTask('A short description');
        $this->validateTaskIsCreated($task);
    }

    /**
     *
     * @test
     *
     */
    public function should_create_a_task_for_null_description()
    {
        $task = $this->createTask(null);
        $this->validateTaskIsCreated($task);
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

    /**
     * Use this function when you want to debug and see on the html in the browser
     * Browse http://localhost:8000/debug.html or whatever name you provide
     * @param string $name
     */

    protected function createDebugFile($name = 'debug.html')
    {
        $debugFile = fopen("../../../web/$name", "w") or die("Unable to open file!");
        fwrite($debugFile, $this->client->getResponse()->getContent());
        fclose($debugFile);
    }

    private function createTask($description = null)
    {
        $task = new Task();
        $task->setName("New Task");
        $task->setCategory("Work");
        $task->setDescription($description);
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

        return $task;
    }

    /**
     * @param $task
     */
    private function validateTaskIsCreated($task)
    {
        $this->client->followRedirect();
        $this->isSuccessful($this->client->getResponse());

        $firstTaskName = $this->getFirstTaskDataColumn()->first()->text();
        $this->assertEquals($task->getName(), $firstTaskName);

        $firstTaskDescription = $this->getFirstTaskDataColumn()->eq(1)->text();
        $this->assertEquals($task->getDescription(), $firstTaskDescription);

        $firstTaskDueDateString = $this->getFirstTaskDataColumn()->eq(2)->text();
        $this->assertEquals('2012-12-25 03:30 PM', $firstTaskDueDateString);
    }

}