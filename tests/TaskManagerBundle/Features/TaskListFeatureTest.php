<?php


namespace TaskManagerBundle\Features;
include_once "FeatureWebTestCase.php";

use TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData;

class TaskListFeatureTest extends FeatureWebTestCase
{

    protected function setUp()
    {
        $this->loadFixturesAndLogin();
    }

    /**
     * @test
     */

    public function should_return_ok_when_access_to_index_page()
    {
        $this->client->request('GET', self::TASK_LIST_ROUTE);
        $this->assertStatusCode(200, $this->client);
    }

    /**
     * @test
     */

    public function should_return_all_not_closed_tasks_inserted_in_the_database_for_logged_in_user()
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
     * @test
     */
    public function should_show_overdue_tasks_with_different_style()
    {
        $overdueStyle = $this->requestTaskIndexPage()->filter('table > tbody > tr')->first()->attr('class');
        $this->assertEquals('overdue', $overdueStyle);
    }

    /**
     * @test
     */
    public function should_not_show_overdue_style_for_not_overdue_tasks()
    {
        $style = $this->requestTaskIndexPage()->filter('table > tbody > tr')->eq(3)->attr('class');
        $this->assertNull($style);
    }

}