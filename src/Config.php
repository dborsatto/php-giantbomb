<?php

/**
 * This file is part of the GiantBomb PHP API created by Davide Borsatto.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2015, Davide Borsatto
 */
namespace DBorsatto\GiantBomb;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config.
 *
 * @author Davide Borsatto <davide.borsatto@gmail.com>
 */
class Config
{
    /**
     * The API key.
     *
     * @var string
     */
    private $apiKey = null;

    /**
     * The API endpoint.
     *
     * @var string
     */
    private $apiEndpoint = null;

    /**
     * The API resource repositories.
     *
     * @var string
     */
    private $repositories = array();

    /**
     * Constructor.
     *
     * @param string $apiKey
     * @param array  $config
     */
    public function __construct($apiKey, array $config = null)
    {
        $this->apiKey = $apiKey;

        // If no configuration is provided, loads the default
        if (!$config) {
            $config = Yaml::parse(file_get_contents(__DIR__.'/Resources/config/api.yml'));
        }

        $this->apiEndpoint = $config['api_endpoint'];
        $this->repositories = $config['repositories'];
    }

    /**
     * Returns the API key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Returns the API endpoint.
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * Returns the repositoy configuration.
     *
     * @return array
     */
    public function getRepositories()
    {
        return $this->repositories;
    }
}
