<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\ApiCallerException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class ApiCaller
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?? new GuzzleClient();
    }

    /**
     * @param string        $apiEndpoint
     * @param string        $apiKey
     * @param CompiledQuery $compiledQuery
     *
     * @throws ApiCallerException
     *
     * @return array
     */
    public function fetch(string $apiEndpoint, string $apiKey, CompiledQuery $compiledQuery): array
    {
        try {
            $url = $apiEndpoint . $compiledQuery->getQueryUrl($apiKey);
            $response = $this->client->request('GET', $url);
        } catch (ClientExceptionInterface $exception) {
            throw ApiCallerException::httpClientError($exception);
        }

        $body = $this->processResponse($response);

        return (array) $body['results'];
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws ApiCallerException
     *
     * @return array
     */
    private function processResponse(ResponseInterface $response): array
    {
        if (200 !== $response->getStatusCode()) {
            throw ApiCallerException::invalidAPIResponse();
        }

        try {
            /** @var array $body */
            $body = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw ApiCallerException::invalidAPIResponse();
        }

        if ('OK' !== $body['error']) {
            throw ApiCallerException::invalidAPIResponse();
        }

        return (array) $body;
    }
}
