<?php


namespace TaskManagerBundle\Features;
include_once "FeatureWebTestCase.php";

class LanguageSelectionFeatureTest extends FeatureWebTestCase
{

    public function setUp()
    {
        $this->client = static::makeClient();
    }

    /**
     * @test
     */
    public function should_change_to_english_when_english_link_is_clicked()
    {
        $request = $this->client->request('GET', '/es');
        $englishLink = $request->selectLink('English')->link();
        $this->client->click($englishLink);
        $locale = $this->client->getRequest()->getLocale();
        $this->assertEquals('en', $locale);

    }

    /**
     * @test
     */
    public function should_change_to_spanish_when_spanish_link_is_clicked()
    {
        $request = $this->client->request('GET', '/en');
        $spanishLink = $request->selectLink('Español')->link();
        $this->client->click($spanishLink);
        $locale = $this->client->getRequest()->getLocale();
        $this->assertEquals('es', $locale);

    }


}