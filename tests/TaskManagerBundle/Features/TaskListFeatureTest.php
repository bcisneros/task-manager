<?php


namespace TaskManagerBundle\Features;


use Liip\FunctionalTestBundle\Test\WebTestCase;
use TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData;

class TaskListFeatureTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'));
    }

    /**
     * @test
     */

    public function should_return_ok_when_access_to_index_page()
    {
        $client = static::makeClient();
        $client->request('GET', '/tasks/');
        $this->assertStatusCode(200, $client);
    }

    /**
     * @test
     */

    public function should_return_all_not_closed_tasks_inserted_in_the_database()
    {
        $tasksCounter = $this->requestTaskIndexPage()->filter('table > tbody > tr')->count();
        $this->assertEquals(4, $tasksCounter);
    }


    /**
     * @test
     */
    public function should_list_tasks_by_due_date_by_ascending_order()
    {
        $firstTaskName = $this->requestTaskIndexPage()->filter('table > tbody > tr')->first()->filter('td')->first()->text();
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME, $firstTaskName);

    }

    /**
     * @test
     */
    public function should_have_actions_button_as_links()
    {
        $this->assertEquals(3, $this->requestTaskIndexPage()->filter('ul.task-actions')->first()->filter('li a')->count());
    }

    /**
     * @test
     */
    public function should_show_total_number_of_tasks()
    {
        $totalString = $this->requestTaskIndexPage()->filter('table > tfoot > tr')->first()->filter('td')->first()->text();
        $this->assertEquals('Total: 4', $totalString);
    }

    /**
     * @test
     */
    public function should_show_due_date_in_Y_m_d_h_i_A_format()
    {
        $formattedDueDate = $this->requestTaskIndexPage()->filter('table > tbody > tr')->first()->filter('td')->eq(2)->text();
        $this->assertEquals('2016-01-01 12:00 AM', $formattedDueDate);

    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function requestTaskIndexPage()
    {
        return static::makeClient()->request('GET', '/tasks/');
    }
}