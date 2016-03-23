<?php


namespace TaskManagerBundle\Features;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class RegisterNewUserFeatureTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = static::makeClient();
        $this->redirectToLoginPage();
    }

    /**
     * @test
     */
    public function should_show_a_registration_link_when_user_is_not_logged_in()
    {
        $this->assertElementIsPresentInPage('ul > li > a[href="/register/"]', 1);
    }

    /**
     * @test
     */
    public function should_not_show_a_registration_link_when_user_is_logged_in()
    {
        $fixtures = $this->loadFixtures(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData'))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin'), 'main');
        $this->client = static::makeClient(true);
        $this->goHomePageAndRedirect();
        $this->assertElementIsPresentInPage('ul > li > a[href="/register/"]', 0);
    }

    /**
     * @test
     */
    public function should_register_a_new_user()
    {
        $link = $this->client->getCrawler()->selectLink('Create an account')->link();
        $this->client->click($link);

        $registerForm = $this->client->getCrawler()
            ->selectButton('Register')
            ->form(array('fos_user_registration_form[email]' => 'email@test.com',
                'fos_user_registration_form[username]' => 'test',
                'fos_user_registration_form[plainPassword]' => 'password'));
        $this->client->submit($registerForm);
        $this->client->followRedirect();
        $this->assertElementIsPresentInPage('p:contains("Congrats test, your account is now activated.")', 1);
    }

    /**
     * Use this function to debug purposes only and stop
     * @param bool $stopExecution By default does not stop execution but you can print and stop
     */
    protected function printResponseContent($stopExecution = false)
    {
        echo $this->client->getResponse()->getContent();
        if ($stopExecution) {
            die;
        }

    }

    /**
     * Use this function when you want to debug and see on the html in the browser
     * Browse http://localhost:8000/debug.html or whatever name you provide
     * @param string $name
     */

    protected function createDebugFile($name = 'debug.html')
    {
        $debugFile = fopen("../../../web/$name", "w") or die("Unable to open file!");
        fwrite($debugFile, $this->client->getResponse()->getContent());
        fclose($debugFile);
    }

    private function redirectToLoginPage()
    {
        $this->goHomePageAndRedirect();
    }

    private function assertElementIsPresentInPage($elementToFind, $times)
    {
        $elementCount = $this->client->getCrawler()->filter($elementToFind)->count();
        $this->assertEquals($times, $elementCount);
    }

    private function goHomePageAndRedirect()
    {
        $this->client->request('GET', '/');
    }
}