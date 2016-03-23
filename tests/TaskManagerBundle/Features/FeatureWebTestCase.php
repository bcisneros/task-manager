<?php

namespace TaskManagerBundle\Features;


use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class FeatureWebTestCase extends WebTestCase
{
    protected $client;

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
}