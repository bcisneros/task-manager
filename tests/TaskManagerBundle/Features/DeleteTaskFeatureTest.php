<?php

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData;

class DeleteTaskFeatureTest extends WebTestCase
{
    const TASK_LIST_ROUTE = 'en/tasks/';

    protected function setUp()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
            'TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
    }

    /**
     * @test
     */
    public function should_delete_a_task_without_comments()
    {
        $client = static::makeClient();
        $deleteTaskLink = $client->request('GET', self::TASK_LIST_ROUTE)
            ->selectLink("Remove")->eq(1)
            ->link();
        $client->click($deleteTaskLink);
        $client->followRedirect();
        $this->assertStatusCode(200, $client);
        $this->assertEquals(0, $client->getCrawler()->filter('html:contains("Send email")')->count());
    }

    /**
     * @test
     */
    public function should_delete_a_task_with_comments()
    {
        $client = static::makeClient();
        $deleteTaskLink = $client->request('GET', self::TASK_LIST_ROUTE)
            ->selectLink("Remove")->first()
            ->link();
        $client->click($deleteTaskLink);
        $client->followRedirect();
        $this->assertStatusCode(200, $client);
        $this->assertEquals(0, $client->getCrawler()->filter('html:contains("' . LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME . '")')->count());
    }

    /**
     * @test
     */
    public function should_delete_a_task_from_show_details_page()
    {
        $client = static::makeClient();
        $deleteTaskLink = $client->request('GET', self::TASK_LIST_ROUTE)
            ->selectLink("Details")->first()
            ->link();
        $client->click($deleteTaskLink);
        $crawler = $client->getCrawler();
        $removeTaskLink = $crawler->selectLink('Remove')->link();
        $client->click($removeTaskLink);
        $client->followRedirect();
        $this->isSuccessful($client->getResponse());
        $this->assertEquals(0, $client->getCrawler()->filter('html:contains("' . LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME . '")')->count());
    }
}