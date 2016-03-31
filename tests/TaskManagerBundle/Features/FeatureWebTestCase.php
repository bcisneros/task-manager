<?php

namespace TaskManagerBundle\Features;


use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class FeatureWebTestCase extends WebTestCase
{
    const TASK_LIST_ROUTE = 'en/tasks/';
    protected $client;
    protected $user;

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

    /**
     * @param int $page
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function requestTaskIndexPage($page = 1)
    {
        return $this->client->request('GET', FeatureWebTestCase::TASK_LIST_ROUTE, array('page' => $page));
    }

    protected function loadFixturesAndLogin($fixturesArray = array('TaskManagerBundle\DataFixtures\ORM\LoadAdminUserData',
        'TaskManagerBundle\DataFixtures\ORM\LoadInitialTaskData'), $user = 'admin', $firewallName = 'main')
    {
        $fixtures = $this->loadFixtures($fixturesArray)->getReferenceRepository();
        $this->user = $fixtures->getReference($user);
        $this->loginAs($this->user, $firewallName);
        $this->client = static::makeClient(true);
    }

    /**
     * @param $element
     * @return mixed
     */
    protected function filter($element)
    {
        return $this->client->getCrawler()->filter($element);
    }

    protected function assertValidationErrorsExists($expected)
    {
        $this->assertValidationErrors($expected, $this->client->getContainer());
    }
}