<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Client;
use DBorsatto\GiantBomb\Config;
use Doctrine\Common\Cache\ArrayCache;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $config = new Config('MyApiKey');
        $this->client = new Client($config);
    }

    public function repositoryProvider()
    {
        return [
            ['Accessory'],
        ];
    }

    private function createGuzzleMock($responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);

        return new GuzzleClient(['handler' => $handler]);
    }

    /**
     * @dataProvider repositoryProvider
     */
    public function testRepositories($name)
    {
        $this->assertInstanceOf('DBorsatto\GiantBomb\Repository', $this->client->getRepository($name));
    }

    /**
     * @dataProvider repositoryProvider
     */
    public function testQuery($name)
    {
        $this->assertInstanceOf('DBorsatto\GiantBomb\Query', $this->client->query($name));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRepository()
    {
        $this->client->getRepository('Invalid');
    }

    public function testValidCacheProvider()
    {
        $this->assertInstanceOf('Doctrine\Common\Cache\CacheProvider', $this->client->getCacheProvider());
    }

    public function testQueryBuilder()
    {
        $reflection = new \ReflectionClass(\get_class($this->client));
        $method = $reflection->getMethod('buildQueryUrl');
        $method->setAccessible(true);

        $baseUrl = 'example/';
        $parameters = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $url = $method->invokeArgs($this->client, [$baseUrl, $parameters]);
        $this->assertSame($url, 'example/?param1=value1&param2=value2&');
    }

    public function testSignatureCreator()
    {
        $reflection = new \ReflectionClass(\get_class($this->client));
        $method = $reflection->getMethod('createSignature');
        $method->setAccessible(true);

        $baseUrl = 'example/';
        $parameters = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $signature = $method->invokeArgs($this->client, [$baseUrl, $parameters]);
        $this->assertSame($signature, 'giantbomb-faed9fe');
    }

    public function testShortcuts()
    {
        $config = new Config('MyApiKey');
        $stubClient = $this->getMockBuilder('\DBorsatto\GiantBomb\Client')
            ->setConstructorArgs([$config, null])
            ->getMock();
        $stubClient->method('loadResource')
            ->will($this->returnCallback(function ($url, $parameters) {
                return [
                    ['url' => $url],
                    ['parameters' => $parameters],
                ];
            }));

        $this->client->getRepository('Game')->setClient($stubClient);
        $model = $this->client->findOne('Game', 'resource_id');
        $this->assertInternalType('array', $model->getValues());

        $this->client->getRepository('Search')->setClient($stubClient);
        $models = $this->client->search('value', 'resource');
        $this->assertSame(\count($models), 2);
        $this->assertInternalType('array', $models[1]->get('parameters'));
    }

    public function testProcessedResponseSuccess()
    {
        $jsonBody = ['error' => 'OK'];
        $guzzle = $this->createGuzzleMock([
            new Response(200, [], \json_encode($jsonBody)),
        ]);

        $response = $guzzle->request('GET', 'https://www.google.com');

        $reflection = new \ReflectionClass(\get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->client, [$response]);
        $this->assertSame($value, $jsonBody);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testProcessedResponseFailureCode()
    {
        $guzzle = $this->createGuzzleMock([
            new Response(301, []),
        ]);

        $response = $guzzle->request('GET', 'https://www.google.com');

        $reflection = new \ReflectionClass(\get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        // This should throw an Exception
        $method->invokeArgs($this->client, [$response]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testProcessedResponseInvalidFormat()
    {
        $guzzle = $this->createGuzzleMock([
            new Response(200, [], '[INVALID JSON}'),
        ]);

        $response = $guzzle->request('GET', 'https://www.google.com');

        $reflection = new \ReflectionClass(\get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        // This should throw an Exception
        $method->invokeArgs($this->client, [$response]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testProcessedResponseErrorPresent()
    {
        $guzzle = $this->createGuzzleMock([
            new Response(200, [], \json_encode(['error' => 'KO'])),
        ]);

        $response = $guzzle->request('GET', 'https://www.google.com');

        $reflection = new \ReflectionClass(\get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        // This should throw an Exception
        $method->invokeArgs($this->client, [$response]);
    }

    public function testLoadResource()
    {
        $firstResult = ['first_result'];
        $secondResult = ['second_result'];
        $guzzle = $this->createGuzzleMock([
            new Response(200, [], \json_encode(['error' => 'OK', 'results' => $firstResult])),
            new Response(200, [], \json_encode(['error' => 'OK', 'results' => $secondResult])),
        ]);

        $client = new Client(new Config('MyApiKey'), new ArrayCache(), $guzzle);

        $value = $client->loadResource('https://www.google.com', ['test' => true]);
        $this->assertSame($value, $firstResult);

        // Test "cached" result
        $value = $client->loadResource('https://www.google.com', ['test' => true]);
        $this->assertSame($value, $firstResult);
    }
}
