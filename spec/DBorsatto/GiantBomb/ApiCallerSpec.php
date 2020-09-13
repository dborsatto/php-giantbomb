<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace spec\DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\CompiledQuery;
use DBorsatto\GiantBomb\Exception\ApiCallerException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function json_encode;

class ApiCallerSpec extends ObjectBehavior
{
    public function let(ClientInterface $client): void
    {
        $this->beConstructedWith($client);
    }

    public function it_fetches(
        ClientInterface $client,
        CompiledQuery $query,
        ResponseInterface $response
    ): void {
        $query->getQueryUrl('api_key')
            ->willReturn('games?api_key=api_key&format=json');

        $result = [
            'error' => 'OK',
            'results' => ['value'],
        ];

        $client->request('GET', 'https://www.example.com/games?api_key=api_key&format=json')
            ->willReturn($response);
        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn(json_encode($result));

        $this->fetch('https://www.example.com/', 'api_key', $query)
            ->shouldBe(['value']);
    }

    public function it_throw_exception_if_client_error(
        ClientInterface $client,
        CompiledQuery $query,
        RequestInterface $request,
        ResponseInterface $response
    ): void {
        $query->getQueryUrl('api_key')
            ->willReturn('games?api_key=api_key&format=json');

        $exception = new ClientException('Error message', $request->getWrappedObject(), $response->getWrappedObject());

        $client->request('GET', 'https://www.example.com/games?api_key=api_key&format=json')
            ->willThrow($exception);

        $this->shouldThrow(ApiCallerException::httpClientError($exception))
            ->during('fetch', ['https://www.example.com/', 'api_key', $query]);
    }

    public function it_throws_with_bad_status_code(
        ClientInterface $client,
        CompiledQuery $query,
        ResponseInterface $response
    ): void {
        $query->getQueryUrl('api_key')
            ->willReturn('games?api_key=api_key&format=json');

        $client->request('GET', 'https://www.example.com/games?api_key=api_key&format=json')
            ->willReturn($response);
        $response->getStatusCode()
            ->willReturn(400);

        $this->shouldThrow(ApiCallerException::invalidAPIResponse())
            ->during('fetch', ['https://www.example.com/', 'api_key', $query]);
    }

    public function it_throws_with_bad_json(
        ClientInterface $client,
        CompiledQuery $query,
        ResponseInterface $response
    ): void {
        $query->getQueryUrl('api_key')
            ->willReturn('games?api_key=api_key&format=json');

        $client->request('GET', 'https://www.example.com/games?api_key=api_key&format=json')
            ->willReturn($response);
        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn(';d}');

        $this->shouldThrow(ApiCallerException::invalidAPIResponse())
            ->during('fetch', ['https://www.example.com/', 'api_key', $query]);
    }

    public function it_throws_with_ko_response(
        ClientInterface $client,
        CompiledQuery $query,
        ResponseInterface $response
    ): void {
        $query->getQueryUrl('api_key')
            ->willReturn('games?api_key=api_key&format=json');

        $result = [
            'error' => 'KO',
            'results' => [],
        ];

        $client->request('GET', 'https://www.example.com/games?api_key=api_key&format=json')
            ->willReturn($response);
        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn(json_encode($result));

        $this->shouldThrow(ApiCallerException::invalidAPIResponse())
            ->during('fetch', ['https://www.example.com/', 'api_key', $query]);
    }
}
