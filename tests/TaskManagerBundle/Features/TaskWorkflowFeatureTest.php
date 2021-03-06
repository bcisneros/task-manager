<?php


namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;


class TaskWorkflowFeatureTest extends WebTestCase
{
    protected $client;

    const TASK_LIST_ROUTE = 'en/tasks/';

    protected function setUp()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
            'TaskManagerBundle\DataFixtures\ORM\LoadWorkflowTaskData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
        $this->client = static::makeClient();
    }

    /**
     * @test
     */
    public function should_show_start_task_link_for_new_tasks()
    {
        $linkCount = $this->client->request('GET', $this->requestTaskById('1'))->selectLink('Start task')->count();
        $this->assertEquals(1, $linkCount);
    }

    /**
     * @test
     */
    public function should_show_close_task_link_for_new_tasks()
    {
        $linkCount = $this->client->request('GET', $this->requestTaskById('2'))->selectLink('Close task')->count();
        $this->assertEquals(1, $linkCount);
    }

    /**
     * @test
     */
    public function should_not_show_any_workflow_link_for_closed_tasks()
    {
        $request = $this->client->request('GET', $this->requestTaskById('3'));
        $startLinkCount = $request->selectLink('Start task')->count();
        $closeLinkCount = $request->selectLink('Close task')->count();
        $this->assertEquals(0, $startLinkCount);
        $this->assertEquals(0, $closeLinkCount);
    }

    /**
     * @test
     */
    public function should_change_start_link_to_close_link_when_start_link_is_clicked()
    {
        $request = $this->client->request('GET', $this->requestTaskById('1'));
        $startTaskLink = $request->selectLink('Start task')->link();
        $this->client->click($startTaskLink);
        $this->client->followRedirect();
        $this->isSuccessful($this->client->getResponse());
        $closeLinkCount = $this->client->getCrawler()->selectLink('Close task')->count();
        $this->assertEquals(1, $closeLinkCount);
    }

    /**
     * @test
     */
    public function should_hide_workflow_links_when_close_link_is_clicked()
    {
        $request = $this->client->request('GET', $this->requestTaskById('2'));
        $startLinkCount = $request->selectLink('Close task')->link();
        $this->client->click($startLinkCount);
        $this->client->followRedirect();
        $crawler = $this->client->getCrawler();
        $closeLinkCount = $crawler->selectLink('Close task')->count();
        $this->assertEquals(0, $closeLinkCount);

        $startLinkCount = $crawler->selectLink('Start task')->count();
        $this->assertEquals(0, $startLinkCount);
    }

    /**
     * @param $taskId
     * @return string
     */
    private function requestTaskById($taskId)
    {
        return self::TASK_LIST_ROUTE . $taskId;
    }
}