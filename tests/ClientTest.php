<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;
use DBorsatto\GiantBomb\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var GuzzleClient
     */
    private $guzzle;

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
     * @expectedException InvalidArgumentException
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
        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('buildQueryUrl');
        $method->setAccessible(true);

        $baseUrl = 'example/';
        $parameters = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $url = $method->invokeArgs($this->client, [$baseUrl, $parameters]);
        $this->assertEquals($url, 'example/?param1=value1&param2=value2&');
    }

    public function testSignatureCreator()
    {
        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('createSignature');
        $method->setAccessible(true);

        $baseUrl = 'example/';
        $parameters = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $signature = $method->invokeArgs($this->client, [$baseUrl, $parameters]);
        $this->assertEquals($signature, 'giantbomb-faed9fe');
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
        $this->assertTrue(is_array($model->getValues()));

        $this->client->getRepository('Search')->setClient($stubClient);
        $models = $this->client->search('value', 'resource');
        $this->assertEquals(count($models), 2);
        $this->assertTrue(is_array($models[1]->get('parameters')));
    }

    public function testProcessedResponseSuccess()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['error' => 'OK'])),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);
        $response = $guzzle->request('GET', 'http://www.google.com');

        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->client, [$response]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testProcessedResponseFailureCode()
    {
        $mock = new MockHandler([
            new Response(301, []),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);
        $response = $guzzle->request('GET', 'http://www.google.com');

        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->client, [$response]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testProcessedResponseInvalidFormat()
    {
        $mock = new MockHandler([
            new Response(200, [], '[INVALID JSON}'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);
        $response = $guzzle->request('GET', 'http://www.google.com');

        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->client, [$response]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testProcessedResponseErrorPresent()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['error' => 'KO'])),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);
        $response = $guzzle->request('GET', 'http://www.google.com');

        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->client, [$response]);
    }

    public function testLoadResource()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['error' => 'OK', 'results' => []])),
            new Response(200, [], json_encode(['error' => 'OK', 'results' => []])),
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $cache = new ArrayCache();
        $config = new Config('MyApiKey');
        $client = new Client($config, $cache, $guzzle);
        $client->loadResource('http://www.google.com', ['test' => true]);
        // Test "cached" result
        $client->loadResource('http://www.google.com', ['test' => true]);
    }
}
