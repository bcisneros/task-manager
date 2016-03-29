<?php

namespace TaskManagerBundle\Features;
include_once "FeatureWebTestCase.php";

class PasswordChangeFeatureTest extends FeatureWebTestCase
{
    const CURRENT_PASSWORD_FIELD_NAME = 'fos_user_change_password_form[current_password]';
    const NEW_PASSWORD_FIELD_NAME = 'fos_user_change_password_form[plainPassword][first]';
    const CONFIRMED_PASSWORD_FIELD_NAME = 'fos_user_change_password_form[plainPassword][second]';

    const NOT_CURRENT_PASSWORD = 'anotherPassword';
    const VALID_NEW_PASSWORD = 'new';
    const CURRENT_ADMIN_PASSWORD = 'admin';
    const DIFFERENT_NEW_PASSWORD = 'different';
    const NULL_PASSWORD = null;

    const NEW_PASSWORD_DATA = 'data.plainPassword';
    const CURRENT_PASSWORD_DATA = 'children[current_password].data';
    const CONFIRM_PASSWORD_DATA = 'children[plainPassword]';
    const CHANGE_PASSWORD_TEXT = 'Change password';

    private $VALID_PARAMETERS = array(self::CURRENT_PASSWORD_FIELD_NAME => self::CURRENT_ADMIN_PASSWORD,
        self::NEW_PASSWORD_FIELD_NAME => self::VALID_NEW_PASSWORD,
        self::CONFIRMED_PASSWORD_FIELD_NAME => self::VALID_NEW_PASSWORD);

    protected function setUp()
    {
        $this->loadFixturesAndLogin(array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData'));
    }

    /**
     * @test
     */
    public function should_show_a_link_to_change_password()
    {
        $this->assertCount(1, $this->selectChangePasswordLink());
    }

    /**
     * @test
     */
    public function should_have_a_change_password_form_when_link_is_clicked()
    {
        $this->assertChangePasswordElementExists('form.fos_user_change_password');
    }

    /**
     * @test
     */
    public function should_have_a_current_password_field_when_link_is_clicked()
    {
        $this->assertChangePasswordElementExists('input[type="password"]#fos_user_change_password_form_current_password');

    }

    /**
     * @test
     */
    public function should_have_a_new_password_field_when_link_is_clicked()
    {
        $this->assertChangePasswordElementExists('input[type="password"]#fos_user_change_password_form_plainPassword_first');

    }

    /**
     * @test
     */
    public function should_have_a_confirm_password_field_when_link_is_clicked()
    {
        $this->assertChangePasswordElementExists('input[type="password"]#fos_user_change_password_form_plainPassword_second');

    }

    /**
     * @test
     */
    public function should_have_a_change_password_button_when_link_is_clicked()
    {
        $this->clickOnChangePasswordLink();
        $this->assertCount(1, $this->selectChangePasswordButton());

    }

    /**
     * @test
     */
    public function should_have_a_cancel_button_when_link_is_clicked()
    {
        $this->clickOnChangePasswordLink();
        $this->assertCount(1, $this->client->getCrawler()->selectLink('Cancel'));

    }

    /**
     * @test
     */
    public function should_validate_current_password()
    {
        $this->submitChangePasswordForm(array(self::CURRENT_PASSWORD_FIELD_NAME => self::NOT_CURRENT_PASSWORD));
        $this->assertValidationErrorsExists(array(self::CURRENT_PASSWORD_DATA));

    }

    /**
     * @test
     */
    public function should_validate_new_password()
    {
        $this->submitChangePasswordForm(array(self::NEW_PASSWORD_FIELD_NAME => self::NULL_PASSWORD,
            self::CONFIRMED_PASSWORD_FIELD_NAME => self::NULL_PASSWORD));
        $this->assertValidationErrorsExists(array(self::NEW_PASSWORD_DATA));

    }

    /**
     * @test
     */
    public function should_validate_confirm_password_is_not_null()
    {
        $this->submitChangePasswordForm(array(self::CONFIRMED_PASSWORD_FIELD_NAME => self::NULL_PASSWORD));
        $this->assertValidationErrorsExists(array(self::CONFIRM_PASSWORD_DATA, self::NEW_PASSWORD_DATA));
    }

    /**
     * @test
     */
    public function should_validate_confirm_password_is_repeated()
    {
        $this->submitChangePasswordForm(array(self::CONFIRMED_PASSWORD_FIELD_NAME => self::DIFFERENT_NEW_PASSWORD));
        $this->assertValidationErrorsExists(array(self::CONFIRM_PASSWORD_DATA, self::NEW_PASSWORD_DATA));
    }

    /**
     * @test
     */
    public function should_change_password_when_everything_is_fine()
    {
        $this->submitChangePasswordForm();
        $this->client->followRedirect();
        $this->assertCount(1, $this->filter('h2:contains("Password was changed successfully!")'));
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function selectChangePasswordLink()
    {
        return $this->requestTaskIndexPage()->selectLink(self::CHANGE_PASSWORD_TEXT);
    }

    private function clickOnChangePasswordLink()
    {
        $link = $this->selectChangePasswordLink()->link();
        $this->client->click($link);
    }

    /**
     * @return mixed
     */
    private function selectChangePasswordButton()
    {
        return $this->client->getCrawler()->selectButton(self::CHANGE_PASSWORD_TEXT);
    }

    private function assertChangePasswordElementExists($element, $times = 1)
    {
        $this->clickOnChangePasswordLink();
        $this->assertCount($times, $this->filter($element));
    }

    private function submitChangePasswordForm($parameters = array())
    {
        $this->clickOnChangePasswordLink();
        $form = $this->selectChangePasswordButton()->form(array_merge($this->VALID_PARAMETERS, $parameters));
        $this->client->submit($form);
    }

}