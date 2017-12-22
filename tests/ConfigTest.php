<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Stub API key.
     *
     * @var string
     */
    private $apiKey = 'MyApiKey';

    /**
     * Stub API endpoint.
     *
     * @var string
     */
    private $apiEndpoint = 'https://www.google.com';

    /**
     * Stub API repositories.
     *
     * @var array
     */
    private $repositories = [
        'Model1' => [],
        'Model2' => [],
        'Model3' => [],
    ];

    /**
     * @var Config
     */
    private $config = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->config = new Config($this->apiKey, [
            'api_endpoint' => $this->apiEndpoint,
            'repositories' => $this->repositories,
        ]);
    }

    public function testApiKey()
    {
        $this->assertSame($this->apiKey, $this->config->getApiKey());
    }

    public function testApiEndpoint()
    {
        $this->assertSame($this->apiEndpoint, $this->config->getApiEndpoint());
    }

    public function testRepositories()
    {
        $this->assertSame($this->repositories, $this->config->getRepositories());
    }
}
