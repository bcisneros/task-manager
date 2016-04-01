<?php

namespace TaskManagerBundle\Features;

include_once 'FeatureWebTestCase.php';

class TaskCommentsFeatureTest extends FeatureWebTestCase
{
    protected $client;

    const TASK_LIST_ROUTE = 'en/tasks/';

    const COMMENT_WITH_MORE_THAN_2000_CHARACTERS = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam accumsan sapien sit amet egestas ultricies. Nulla auctor nibh ipsum, ac volutpat est pretium non. Aliquam finibus, justo et fringilla tincidunt, lacus erat vulputate ante, vel commodo nunc quam at odio. Ut eget condimentum justo. Nulla fermentum nunc ac enim eleifend lacinia. Pellentesque varius neque gravida dui lacinia, eu imperdiet lacus consectetur. Pellentesque scelerisque porta purus sit amet blandit. Nam fringilla lorem quam, ut semper ante tincidunt quis. Nulla euismod mi eget gravida maximus. Nullam sit amet pretium justo, sed cursus dolor. Vestibulum massa lacus, condimentum et suscipit id, tempor ac mi. Nunc consectetur ex mi, non semper orci finibus eu. In venenatis dui nec felis rhoncus, in finibus ante scelerisque. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.

Mauris posuere rutrum est eu lacinia. Nunc condimentum venenatis lorem nec gravida. Fusce interdum efficitur dui, et semper mauris commodo vel. Donec euismod feugiat nibh vel varius. Nullam non mauris ligula. Etiam ultrices odio felis, eu imperdiet nisi faucibus commodo. Duis sed congue tortor. Pellentesque dapibus faucibus mauris, a commodo mauris ultrices eget. Vestibulum sed mi dictum, ultricies felis a, fermentum ligula. Vestibulum eu porttitor leo. Proin id iaculis mi.

Sed a efficitur nibh. Etiam vitae arcu vestibulum, condimentum magna laoreet, facilisis libero. Curabitur feugiat metus mauris, at malesuada diam commodo ac. Vestibulum sed posuere dolor, a sollicitudin orci. Nulla facilisi. Aliquam vitae cursus turpis, in vulputate metus. Aenean lectus mauris, finibus quis ornare id, rhoncus sit amet nulla. Duis ullamcorper odio sit amet risus ornare, sagittis suscipit nibh rutrum. Fusce sodales massa nisl, ac ultricies mauris porta eget. Pellentesque dapibus dolor est. Donec nec neque eu ipsum maximus vulputate. Duis imperdiet id tellus vel lobortis. Quisque vel feugiat erat, at metus.';

    const COMMENT_WITH_LESS_THAN_2000_CHARACTERS = 'New comment';

    const COMMENT_WITH_2000_CHARACTERS = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin fermentum arcu velit, vel fringilla diam placerat eu. Vestibulum id urna porta, consectetur nisl ut, rhoncus risus. Aenean id scelerisque nisl. Donec justo velit, ullamcorper quis tristique luctus, ullamcorper sed nunc. Etiam vitae quam lectus. Sed sit amet nisi ligula. Cras vehicula arcu vel neque tempor, at tempor neque rhoncus. Quisque facilisis, massa et cursus tristique, leo ante dignissim est, at dictum tellus leo eu neque. Nulla justo ante, ultricies quis faucibus in, egestas tincidunt tellus. Duis ac tempor enim, vel malesuada velit. Quisque nec mauris quis ante tincidunt bibendum eu et sapien. Nunc imperdiet ante nec elit feugiat cursus. Etiam sed commodo turpis. Suspendisse bibendum mollis arcu eget feugiat. Pellentesque at felis et leo dignissim malesuada a nec lacus. Curabitur blandit sapien vitae tempor molestie. Donec sollicitudin rhoncus metus. Ut vitae enim eros. Sed a rutrum arcu. Vivamus sit amet velit sed orci sodales pulvinar in vel leo. Maecenas pellentesque interdum ex, at dapibus neque efficitur vitae. Aliquam ligula arcu, molestie pharetra massa a, elementum vulputate nisl. Donec tincidunt dignissim blandit. Quisque tincidunt id mi mollis lobortis. Donec suscipit placerat consectetur. In a convallis augue, vel congue est. Mauris tempus nisi eu orci gravida, sed convallis metus dapibus. Nam quis volutpat magna, non pretium ex. Aenean arcu mi, luctus at nisi vitae, tempus posuere diam. Fusce eu vulputate lectus, eu vestibulum turpis. Phasellus finibus, augue ut tristique ultricies, diam tortor elementum mauris, sed fringilla ante sem eget risus.Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed lacinia ornare est eget laoreet. Ut in dui interdum, dictum ipsum sed, iaculis felis. Nunc luctus id libero tempus bibendum. Maecenas massa libero, posuere quis viverra ac, suscipit eget diam. Morbi aliquam nunc at iaculis fermentum. Vivamus vel pis...';

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
        $this->assertCommentWasCreated(self::COMMENT_WITH_LESS_THAN_2000_CHARACTERS);
    }

    /**
     * @test
     */
    public function should_add_comment_equal_to_2000_characters_length()
    {
        $this->assertCommentWasCreated(self::COMMENT_WITH_2000_CHARACTERS);
    }

    /**
     * @test
     */
    public function should_add_comment_equal_to_4000_characters_length()
    {
        $this->assertCommentWasCreated(self::COMMENT_WITH_2000_CHARACTERS . self::COMMENT_WITH_2000_CHARACTERS);
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

    /**
     * @test
     */

    public function should_validate_comment_length_less_or_equal_to_4000_characters()
    {
        $this->submitComment('2', self::COMMENT_WITH_MORE_THAN_2000_CHARACTERS . self::COMMENT_WITH_MORE_THAN_2000_CHARACTERS);
        $this->assertValidationErrors(array('data.comment'), $this->client->getContainer());
        $this->isSuccessful($this->client->getResponse());
    }

    /**
     * @test
     */
    public function should_add_line_break_when_new_line_is_inserted()
    {
        $this->submitCommentAndRedirect('2', "Line1\nLine2");
        $this->assertCount(1, $this->filter('ul.comment-list > li > p > br'));
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

    private function submitComment($taskId, $comment)
    {
        $addCommentLink = $this->client->request('GET', $this->requestTaskById($taskId))->selectLink('Add comment')->link();
        $this->client->click($addCommentLink);
        $form = $this->client->getCrawler()->selectButton('Add comment')->form(array('comment[comment]' => $comment));
        $this->client->submit($form);
    }

    private function assertCommentWasCreated($comment)
    {
        $this->submitCommentAndRedirect('2', $comment);
        $newCommentCount = $this->client->getCrawler()->filter('html:contains("' . $comment . '")')->count();
        $this->assertEquals(1, $newCommentCount);
    }

    /**
     * @param $taskId
     * @param $comment
     */
    private function submitCommentAndRedirect($taskId, $comment)
    {
        $this->submitComment($taskId, $comment);
        $this->client->followRedirect();
    }
}