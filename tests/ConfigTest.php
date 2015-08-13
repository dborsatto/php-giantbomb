<?php

use DBorsatto\GiantBomb\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $apiKey = 'MyApiKey';

    private $apiEndpoint = 'http://www.google.com';

    private $repositories = array(
        'Model1' => array(),
        'Model2' => array(),
        'Model3' => array(),
    );

    private $config = null;

    public function setUp()
    {
        $this->config = new Config($this->apiKey, array('api_endpoint' => $this->apiEndpoint, 'repositories' => $this->repositories));
    }

    public function testApiKey()
    {
        $this->assertEquals($this->apiKey, $this->config->getApiKey());
    }

    public function testApiEndpoint()
    {
        $this->assertEquals($this->apiEndpoint, $this->config->getApiEndpoint());
    }

    public function testRepositories()
    {
        $this->assertEquals($this->repositories, $this->config->getRepositories());
    }
}
