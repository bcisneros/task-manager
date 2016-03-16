<?php


namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData;


class ShowTaskDetailsFeatureTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'));
        $this->client = static::makeClient();
    }

    /**
     * @test
     */
    public function should_return_ok_when_show_details_page_is_requested()
    {
        $this->clickOnTaskDetailsLink();
        $this->isSuccessful($this->client->getResponse());
    }

    /**
     * @test
     */
    public function should_show_the_tasks_details()
    {
        $this->clickOnTaskDetailsLink();
        $this->assertEquals(4, $this->getTaskDetail($this->getTableRows()->first()));
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME, $this->getTaskDetail($this->getTableRows()->eq(1)));
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_DESCRIPTION, $this->getTaskDetail($this->getTableRows()->eq(2)));
        $this->assertEquals('01-01-2016 12:00 AM', $this->getTaskDetail($this->getTableRows()->eq(3)));
        $this->assertEquals('25-12-2015 02:16 AM', $this->getTaskDetail($this->getTableRows()->eq(4)));
        $this->assertEquals('Work', $this->getTaskDetail($this->getTableRows()->eq(5)));
        $this->assertEquals('Urgent', $this->getTaskDetail($this->getTableRows()->eq(6)));
        $this->assertEquals('In progress', $this->getTaskDetail($this->getTableRows()->eq(7)));
    }

    /**
     * @test
     */
    public function should_return_to_task_list_when_back_to_list_link_is_clicked()
    {
        $this->clickOnTaskDetailsLink();
        $crawler = $this->client->getCrawler();
        $backToListLink = $crawler->selectLink('Back to the list')->link();
        $this->client->click($backToListLink);
        $this->isSuccessful($this->client->getResponse());
        $firstTaskName = $this->client->getCrawler()->filter('table > tbody > tr')->first()->filter('td')->first()->text();
        $this->assertEquals(LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME, $firstTaskName);

    }

    private function clickOnTaskDetailsLink()
    {
        $taskDetailsLink = $this->client->request('GET', '/tasks/')->selectLink('Details')->first()->link();
        $this->client->click($taskDetailsLink);
    }

    /**
     * @return mixed
     */
    private function getTableRows()
    {
        return $this->client->getCrawler()->filter('table > tbody > tr');
    }

    private function getTaskDetail(Crawler $row)
    {
        return $row->filter('td')->first()->text();
    }

}