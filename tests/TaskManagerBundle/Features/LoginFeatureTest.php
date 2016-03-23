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
        $this->redirectToLoginPage();
        $this->assertLoginFormIsShown();
    }

    /**
     * @test
     */
    public function should_log_in_when_credentials_are_ok()
    {
        $this->performLogin();
        $this->assertEquals('Symfony\Bundle\FrameworkBundle\Controller\TemplateController::templateAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @test
     */
    public function should_show_a_welcome_message_when_user_is_logged_in()
    {
        $this->performLogin();
        $this->assertElementIsPresentInPage('li.welcome:contains("Welcome admin")', 1);
    }

    /**
     * @test
     */
    public function should_not_show_a_welcome_message_when_user_is_not_logged_in()
    {
        $this->redirectToLoginPage();
        $this->assertElementIsPresentInPage('li.welcome', 0);
    }

    /**
     * @test
     */
    public function should_not_log_in_when_credentials_are_bad()
    {

        $this->redirectToLoginPage();
        $loginForm = $this->client->getCrawler()->selectButton('Log in')->form(array('_username' => 'nobody', '_password' => 'exists'));
        $this->submitFormAndFollowRedirect($loginForm);
        $this->assertLoginFormIsShown();
    }

    private function redirectToLoginPage()
    {
        $this->client = static::makeClient(true);
        $loginLink = $this->client->request('GET', '/')->selectLink('Login')->link();
        $this->client->click($loginLink);
        //$this->client->followRedirect();
        //$this->client->followRedirect();
    }

    private function assertElementIsPresentInPage($elementToFind, $times)
    {
        $elementCount = $this->client->getCrawler()->filter($elementToFind)->count();
        $this->assertEquals($times, $elementCount);
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

    private function performLogin()
    {
        $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData'));
        $this->client = static::makeClient(true);
        $this->redirectToLoginPage();
        $loginForm = $this->client->getCrawler()->selectButton('Log in')->form(array('_username' => 'admin', '_password' => 'admin'));
        $this->submitFormAndFollowRedirect($loginForm);
    }
}