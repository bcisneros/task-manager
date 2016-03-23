<?php

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;


class TaskCommentsFeatureTest extends WebTestCase
{
    protected $client;

    const TASK_LIST_ROUTE = 'en/tasks/';

    protected function setUp()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
            'TaskManagerBundle\DataFixtures\ORM\LoadTaskCommentsData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
        $this->client = static::makeClient();
    }

    /**
     * @test
     */
    public function should_show_comments_for_a_task_with_comments()
    {
        $request = $this->client->request('GET', $this->requestTaskById('1'));
        $commentsCount = $request->filter('ul.comment-list > li')->count();
        $this->assertEquals(1, $commentsCount);
    }

    /**
     * @test
     */
    public function should_show_no_comments_message_for_a_task_without_comments()
    {
        $this->client->request('GET', $this->requestTaskById('2'));
        $this->assertNoCommentsExists();
    }

    /**
     * @test
     */
    public function should_add_comments_to_tasks()
    {
        $addCommentLink = $this->client->request('GET', $this->requestTaskById('2'))->selectLink('Add comment')->link();
        $this->client->click($addCommentLink);
        $form = $this->client->getCrawler()->selectButton('Add comment')->form(array('comment[comment]' => 'New comment'));
        $this->client->submit($form);
        $this->client->followRedirect();
        $newCommentCount = $this->client->getCrawler()->filter('html:contains("New comment")')->count();
        $this->assertEquals(1, $newCommentCount);
    }

    /**
     * @test
     */

    public function should_cancel_action_when_cancel_link_is_clicked()
    {
        $addCommentLink = $this->client->request('GET', $this->requestTaskById('2'))->selectLink('Add comment')->link();
        $this->client->click($addCommentLink);
        $cancelLink = $this->client->getCrawler()->selectLink('Cancel')->link();
        $this->client->click($cancelLink);
        $this->assertNoCommentsExists();
    }

    private function assertNoCommentsExists()
    {
        $noCommentsMessageCount = $this->client->getCrawler()->filter('html:contains("No comments yet.")')->count();
        $this->assertEquals(1, $noCommentsMessageCount);
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