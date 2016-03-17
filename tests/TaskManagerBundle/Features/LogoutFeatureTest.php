<?php
/**
 * Created by PhpStorm.
 * User: cisneben
 * Date: 17/03/2016
 * Time: 12:35 PM
 */

namespace TaskManagerBundle\Features;
use Liip\FunctionalTestBundle\Test\WebTestCase;


class LogoutFeatureTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
            'TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
        $this->client = static::makeClient(true);
    }

    /**
     * @test
     */
    public function should_show_a_logout_link_when_user_is_logged_in()
    {
        $this->requestHomePageAndFollowRedirect();
        $this->assertElementIsPresentInPage('a[href="/logout"]', 1);
    }

    /**
     * @test
     */
    public function should_logout_when_logout_link_is_clicked()
    {
        $this->requestHomePageAndFollowRedirect();
        $this->clickOnLogoutLinkAndRedirect();
        $this->assertElementIsPresentInPage('form.login', 1);
    }

    /**
     * @test
     */
    public function should_not_show_logout_link_when_user_is_not_logged_in()
    {
        $this->requestHomePageAndFollowRedirect();
        $this->clickOnLogoutLinkAndRedirect();
        $this->assertElementIsPresentInPage('a[href="/logout"]', 0);
    }

    /**
     * Use this function to debug purposes only
     */
    protected function printResponseContent()
    {
        echo $this->client->getResponse()->getContent();
        die;
    }

    private function requestHomePageAndFollowRedirect()
    {
        $this->client->request('GET', '/');
        $this->client->followRedirect();
    }

    private function clickOnLogoutLinkAndRedirect()
    {
        $logoutLink = $this->client->getCrawler()->selectLink('Logout')->link();
        $this->client->click($logoutLink);
        $this->client->followRedirects();
        $this->client->followRedirect();
    }

    private function assertElementIsPresentInPage($elementToFind, $times)
    {
        $elementCount = $this->client->getCrawler()->filter($elementToFind)->count();
        $this->assertEquals($times, $elementCount);
    }
}