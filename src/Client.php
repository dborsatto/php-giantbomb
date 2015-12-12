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
use GuzzleHttp\Psr7\Response;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\VoidCache;

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
     * @var Cache
     */
    private $cache = null;

    /**
     * @var GuzzleClient
     */
    private $guzzle = null;

    /**
     * Class constructor.
     *
     * @param Config $config
     * @param CacheProvider $cache
     * @param GuzzleClient $guzzle
     */
    public function __construct(Config $config, CacheProvider $cache = null, GuzzleClient $guzzle = null)
    {
        $this->config = $config;
        $this->setCacheProvider($cache);
        $this->initializeRepositories($config->getRepositories());
        if (!$guzzle) {
            $guzzle = new GuzzleClient();
        }
        $this->guzzle = $guzzle;
    }

    /**
     * Sets the current cache provider
     *
     * @param CacheProvider $cache
     *
     * @return Client
     */
    public function setCacheProvider(CacheProvider $cache = null)
    {
        if (!$cache) {
            $cache = new VoidCache();
        }
        $this->cache = $cache;

        return $this;
    }

    /**
     * Returns the current cache provider
     *
     * @return CacheProvider
     */
    public function getCacheProvider()
    {
        return $this->cache;
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
            throw new \InvalidArgumentException(sprintf(
                'The name %s is not a valid repository, try one of %s',
                $name,
                implode(', ', array_keys($this->repositories))
            ));
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
     * @param array $parameters
     *
     * @return array
     */
    public function loadResource($url, $parameters)
    {
        $signature = $this->createSignature($url, $parameters);
        if ($this->cache->contains($signature)) {
            return $this->cache->fetch($signature);
        }

        $parameters['format'] = 'json';
        $parameters['api_key'] = $this->config->getApiKey();
        $url = $this->config->getApiEndpoint().$this->buildQueryUrl($url, $parameters);

        $response = $this->guzzle->request('GET', $url);
        $body = $this->processResponse($response);

        $this->cache->save($signature, $body['results']);

        return $body['results'];
    }

    /**
     * Checks if the Response object is valid, and throws an exception if not
     *
     * @param  Response $response
     *
     * @return array The response body
     */
    private function processResponse(Response $response)
    {
        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Query to the API server did not result in an appropriate response code');
        }

        $body = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('There was an error parsing the response JSON: '.json_last_error_msg());
        }

        if ($body['error'] != 'OK') {
            throw new \RuntimeException('Query to the API server did not result in an appropriate response code');
        }

        return $body;
    }

    /**
     * Builds an url using the local Config and the given parameters
     *
     * @param  string $url
     * @param  array $parameters
     *
     * @return string
     */
    private function buildQueryUrl($url, $parameters)
    {
        $query = '';
        foreach ($parameters as $name => $value) {
            $query .= $name.'='.$value.'&';
        }

        return $url.'?'.$query;
    }

    /**
     * Creates a signature for the given request
     *
     * @param  string $url
     * @param  array $parameters
     *
     * @return string
     */
    private function createSignature($url, $parameters)
    {
        return 'giantbomb-'.substr(sha1($this->buildQueryUrl($url, $parameters)), 0, 7);
    }
}
