<?php

namespace TaskManagerBundle\Features;
include_once 'FeatureWebTestCase.php';

class TaskPaginationFeatureTest extends FeatureWebTestCase
{
    protected function setUp()
    {
        $this->loadFixturesAndLogin(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
            'TaskManagerBundle\DataFixtures\ORM\LoadTasksForPaginationData'));
    }

    /**
     * @test
     */
    public function should_list_10_tasks_per_page()
    {
        $this->requestTaskIndexPage();
        $this->assertCount(10, $this->filter('table > tbody > tr'));
    }

    /**
     * @test
     */
    public function should_include_navigation_links()
    {
        $this->requestTaskIndexPage(2);
        $this->assertCount(1, $this->filter('.pagination .first'));
        $this->assertCount(1, $this->filter('.pagination .previous'));
        $this->assertCount(1, $this->filter('.pagination .next'));
        $this->assertCount(1, $this->filter('.pagination .last'));
        $this->assertCount(9, $this->filter('.pagination .page a'));
    }

    /**
     * @test
     */
    public function should_not_create_a_link_for_current_page()
    {
        $this->requestTaskIndexPage(2);
        $this->assertCount(0, $this->filter('.pagination .page a:contains("2")'));
    }

    /**
     * @test
     */
    public function should_not_show_first_page_when_page_is_1()
    {
        $this->requestTaskIndexPage(1);
        $this->assertCount(0, $this->filter('.pagination .first'));
    }

    /**
     * @test
     */
    public function should_not_show_previous_page_when_page_is_1()
    {
        $this->requestTaskIndexPage(1);
        $this->assertCount(0, $this->filter('.pagination .previous'));
    }

    /**
     * @test
     */
    public function should_not_show_next_page_when_page_is_last_page()
    {
        $this->requestTaskIndexPage(10);
        $this->assertCount(0, $this->filter('.pagination .next'));
    }

    /**
     * @test
     */
    public function should_not_show_last_page_when_page_is_last_page()
    {
        $this->requestTaskIndexPage(10);
        $this->assertCount(0, $this->filter('.pagination .last'));
    }

    /**
     * @test
     */
    public function should_show_a_message_with_pagination_info()
    {
        $this->requestTaskIndexPage(2);
        $this->assertEquals("Page 2 of 10, Tasks 11 - 20 of 95", $this->filter('.pagination-info')->text());
    }

}