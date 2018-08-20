<?php

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license   MIT
 */

namespace DBorsatto\GiantBomb;

use function GuzzleHttp\json_decode as guzzle_json_decode;

/**
 * Config class.
 */
class Config
{
    /**
     * The API key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * The API endpoint.
     *
     * @var string
     */
    private $apiEndpoint;

    /**
     * The API resource repositories.
     *
     * @var array
     */
    private $repositories = [];

    /**
     * Constructor.
     *
     * @param string $apiKey
     * @param array  $config
     */
    public function __construct(string $apiKey, array $config = [])
    {
        $this->apiKey = $apiKey;

        // If no configuration is provided, loads the default
        if (!$config) {
            $config = guzzle_json_decode(
                \file_get_contents(__DIR__.'/Resources/config/api.json'),
                true
            );
        }

        $this->apiEndpoint = $config['api_endpoint'];
        $this->repositories = $config['repositories'];
    }

    /**
     * Returns the API key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Returns the API endpoint.
     *
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * Returns the repository configuration.
     *
     * @return array
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }
}
