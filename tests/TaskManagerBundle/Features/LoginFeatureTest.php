<?php

namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class LoginFeatureTest extends WebTestCase
{
    protected $client;

    /**
     * @test
     */
    public function should_show_a_login_form_when_user_is_not_logged_in()
    {
        $this->client = static::makeClient();
        $this->redirectToLoginPage();
        $this->assertLoginFormIsShown();
    }

    /**
     * @test
     */
    public function should_log_in_when_credentials_are_ok()
    {
        $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData'));
        $this->client = static::makeClient(true);
        $this->redirectToLoginPage();
        $loginForm = $this->client->getCrawler()->selectButton('Log in')->form(array('_username' => 'admin', '_password' => 'admin'));
        $this->submitFormAndFollowRedirect($loginForm);
        $this->assertEquals('TaskManagerBundle\Controller\TaskController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @test
     */
    public function should_not_log_in_when_credentials_are_bad()
    {
        $this->client = static::makeClient(true);
        $this->redirectToLoginPage();
        $loginForm = $this->client->getCrawler()->selectButton('Log in')->form(array('_username' => 'nobody', '_password' => 'exists'));
        $this->submitFormAndFollowRedirect($loginForm);
        $this->assertLoginFormIsShown();
    }

    private function redirectToLoginPage()
    {
        $this->client->request('GET', '/');
        $this->client->followRedirect();
        $this->client->followRedirect();
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
     * @param $loginForm
     */
    private function submitFormAndFollowRedirect($loginForm)
    {
        $this->client->submit($loginForm);
        $this->client->followRedirect();
    }

    private function assertLoginFormIsShown()
    {
        $loginFormCount = $this->client->getCrawler()->filter('form.login')->count();
        $this->assertEquals(1, $loginFormCount);
    }
}