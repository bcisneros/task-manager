<?php

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Config\Definition\Exception\Exception;


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
        $this->assertElementIsPresentInPage('body#homepage', 1);
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

    /**
     * Use this function when you want to debug and see on the html in the browser
     * Browse http://localhost:8000/debug.html or whatever name you provide
     * @param string $name
     */

    protected function createDebugFile($name = 'debug.html')
    {
        try {
            $debugFile = fopen("../../../web/$name", "w") or die("Unable to open file!");
            fwrite($debugFile, $this->client->getResponse()->getContent());
            fclose($debugFile);
        } catch (Exception $exception) {
            echo "Could not create $name file";
        }

    }

    private function requestHomePageAndFollowRedirect()
    {
        $this->client->request('GET', '/');
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