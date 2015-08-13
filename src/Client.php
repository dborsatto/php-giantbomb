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

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class Client.
 *
 * @author Davide Borsatto <davide.borsatto@gmail.com>
 */
class Client
{
    /**
     * @var Config
     */
    private $config = null;

    /**
     * @var array
     */
    private $repositories = array();

    /**
     * Class constructor.
     *
     * @param Config $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->initializeRepositories($config->getRepositories());
    }

    /**
     * Initializes the API repositories.
     *
     * @param array $repositories
     */
    private function initializeRepositories(array $repositories)
    {
        foreach ($repositories as $name => $data) {
            $this->repositories[$name] = new Repository($this, $name, $data);
        }
    }

    /**
     * Returns a Repository.
     *
     * @param string $name
     *
     * @return Repository
     */
    public function getRepository($name)
    {
        if (!isset($this->repositories[$name])) {
            throw new InvalidArgumentException(sprintf('The name %s is not a valid repository, try one of %s', $name, implode(', ', array_keys($this->repositories))));
        }

        return $this->repositories[$name];
    }

    /**
     * Shortcut for creating a query for the given repository.
     *
     * @param string $name
     *
     * @return Query
     */
    public function query($name)
    {
        return $this->getRepository($name)->query();
    }

    /**
     * Shortcut for finding a single element.
     *
     * @param string $name
     * @param string $resourceId
     *
     * @return Model
     */
    public function findOne($name, $resourceId)
    {
        return $this->getRepository($name)->query()->setResourceId($resourceId)->findOne();
    }

    /**
     * Shortcut for searching.
     *
     * @param string $string
     * @param string $resources
     *
     * @return array
     */
    public function search($string, $resources = '')
    {
        $query = $this->getRepository('Search')->query()->setParameter('query', $string);
        if ($resources) {
            $query->setParameter('resources', strtolower($resources));
        }

        return $query->find();
    }

    /**
     * Loads a HTTP resource.
     *
     * @param string $url
     * @param array  $parameters
     * @param string $type
     *
     * @return array
     */
    public function loadResource($url, $parameters, $type)
    {
        $parameters['format'] = 'json';
        $parameters['api_key'] = $this->config->getApiKey();
        $query = '';
        foreach ($parameters as $name => $value) {
            $query .= $name.'='.$value.'&';
        }

        $url = $this->config->getApiEndpoint().$url.'?'.$query;

        $client = new GuzzleClient();
        $response = $client->get($url);

        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Query to GiantBomb did not result in an appropriate response code');
        }
        $contentType = $response->getHeader('content-type');
        if (is_array($contentType)) {
            $contentType = $contentType[0];
        }
        if ($contentType != 'application/json; charset=utf-8') {
            throw new \RuntimeException(sprintf('Query to GiantBomb did not provide the right type of data (content type received %s)', $contentType));
        }

        $body = json_decode($response->getBody()->getContents(), true);

        if ($body['error'] != 'OK') {
            throw new \RuntimeException('Query to GiantBomb did not result in an appropriate response code');
        }

        return $body['results'];
    }
}
