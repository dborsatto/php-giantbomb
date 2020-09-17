<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use Cache\Adapter\Void\VoidCachePool;
use DBorsatto\GiantBomb\Exception\ApiCallerException;
use DBorsatto\GiantBomb\Exception\ClientException;
use DBorsatto\GiantBomb\Exception\SdkException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function mb_strtolower;

class Client implements ClientInterface
{
    private Configuration $configuration;

    private CacheInterface $cache;

    private ApiCaller $apiCaller;

    public function __construct(Configuration $configuration, CacheInterface $cache = null, ApiCaller $apiCaller = null)
    {
        $this->configuration = $configuration;
        $this->cache = $cache ?? new VoidCachePool();
        $this->apiCaller = $apiCaller ?? new ApiCaller();
    }

    public function find(string $repositoryName, Query $query): array
    {
        $repositoryConfiguration = $this->configuration->getRepository($repositoryName);
        $compiledQuery = CompiledQuery::createForCollection($repositoryConfiguration, $query);

        $results = $this->callApi($compiledQuery);

        $models = [];
        /** @var array<string, mixed> $result */
        foreach ($results as $result) {
            $models[] = new Model($repositoryConfiguration->getName(), $result);
        }

        return $models;
    }

    public function findOne(string $repositoryName, Query $query): Model
    {
        $repositoryConfiguration = $this->configuration->getRepository($repositoryName);
        $compiledQuery = CompiledQuery::createForSingle($repositoryConfiguration, $query);

        /** @var array<string, mixed> $result */
        $result = $this->callApi($compiledQuery);

        return new Model($repositoryConfiguration->getName(), $result);
    }

    /**
     * @param string $repositoryName
     * @param string $resourceID
     *
     * @throws SdkException
     *
     * @return Model
     */
    public function findWithResourceID(string $repositoryName, string $resourceID): Model
    {
        return $this->findOne($repositoryName, Query::createForResourceId($resourceID));
    }

    /**
     * @param string $string
     * @param string $resources
     *
     * @throws SdkException
     *
     * @return Model[]
     */
    public function search(string $string, string $resources = ''): array
    {
        $query = Query::createWithParameter('query', $string);

        if ($resources) {
            $query->setParameter('resources', mb_strtolower($resources));
        }

        return $this->find('Search', $query);
    }

    /**
     * @param CompiledQuery $compiledQuery
     *
     * @throws ClientException
     * @throws ApiCallerException
     *
     * @return array
     */
    private function callApi(CompiledQuery $compiledQuery): array
    {
        $signature = $compiledQuery->getSignature();

        try {
            if ($this->cache->has($signature)) {
                return (array) $this->cache->get($signature);
            }
        } catch (InvalidArgumentException $exception) {
            throw ClientException::cacheError($exception);
        }

        $results = $this->apiCaller->fetch(
            $this->configuration->getApiEndpoint(),
            $this->configuration->getApiKey(),
            $compiledQuery
        );

        try {
            $this->cache->set($signature, $results);
        } catch (InvalidArgumentException $exception) {
            throw ClientException::cacheError($exception);
        }

        return $results;
    }
}
