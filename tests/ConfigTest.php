<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Stub API key
     *
     * @var string
     */
    private $apiKey = 'MyApiKey';

    /**
     * Stub API endpoint
     *
     * @var string
     */
    private $apiEndpoint = 'http://www.google.com';

    /**
     * Stub API repositories
     *
     * @var array
     */
    private $repositories = array(
        'Model1' => array(),
        'Model2' => array(),
        'Model3' => array(),
    );

    /**
     * @var Config
     */
    private $config = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->config = new Config($this->apiKey, array(
                'api_endpoint' => $this->apiEndpoint,
                'repositories' => $this->repositories)
            );
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
