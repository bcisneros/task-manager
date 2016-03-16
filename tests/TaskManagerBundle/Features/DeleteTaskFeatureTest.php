<?php
/**
 * Created by PhpStorm.
 * User: cisneben
 * Date: 15/03/2016
 * Time: 11:04 AM
 */

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData;

class DeleteTaskFeatureTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'));
    }

    /**
     * @test
     */
    public function should_delete_a_task_that_has_comments() {
        $client = static::makeClient();
        $deleteTaskLink = $client->request('GET', '/tasks/')
            ->selectLink("Remove")->first()
            ->link();
        $client->click($deleteTaskLink);
        $client->followRedirect();
        $this->assertStatusCode(200, $client);
        $this->assertEquals(0, $client->getCrawler()->filter('html:contains("'.LoadInitialTaskData::OLDEST_DUE_DATE_TASK_NAME.'")')->count());

    }
}