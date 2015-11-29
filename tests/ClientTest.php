<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;
use DBorsatto\GiantBomb\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

class ClientTest extends \PHPUnit_Framework_TestCase
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
        return array(
            array('Accessory'),/*
            array('Character'),
            array('Chat'),
            array('Company'),
            array('Concept'),
            array('Franchise'),
            array('Game'),
            array('GameRating'),
            array('Genre'),
            array('Location'),
            array('Object'),
            array('Person'),
            array('Platform'),
            array('Promo'),
            array('RatingBoard'),
            array('Region'),
            array('Release'),
            array('Review'),
            array('Search'),
            array('Theme'),
            array('Type'),
            array('UserReview'),
            array('Video'),
            array('VideoType'),*/
        );
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
        $parameters = array(
            'param1' => 'value1',
            'param2' => 'value2',
        );

        $url = $method->invokeArgs($this->client, array($baseUrl, $parameters));
        $this->assertEquals($url, 'example/?param1=value1&param2=value2&');
    }

    public function testSignatureCreator()
    {
        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('createSignature');
        $method->setAccessible(true);

        $baseUrl = 'example/';
        $parameters = array(
            'param1' => 'value1',
            'param2' => 'value2',
        );

        $signature = $method->invokeArgs($this->client, array($baseUrl, $parameters));
        $this->assertEquals($signature, 'faed9fe');
    }

    public function testShortcuts()
    {
        $config = new Config('MyApiKey');
        $stubClient = $this->getMockBuilder('\DBorsatto\GiantBomb\Client')
            ->setConstructorArgs(array($config, null))
            ->getMock();
        $stubClient->method('loadResource')
            ->will($this->returnCallback(function ($url, $parameters) {
                return array(
                    array('url' => $url),
                    array('parameters' => $parameters),
                );
            }));

        $this->client->getRepository('Game')->setClient($stubClient);
        $model = $this->client->findOne('Game', 'resource_id');
        $this->assertTrue(is_array($model->getValues()));

        $this->client->getRepository('Search')->setClient($stubClient);
        $models = $this->client->search('value', 'resource');
        $this->assertEquals(count($models), 2);
        $this->assertTrue(is_array($models[1]->get('parameters')));
    }

    public function testProcessedResponse()
    {
        $url = 'http://mysafeinfo.com/api/data?list=programminglanguages&format=json';
        $client = new GuzzleClient();
        $response = $client->get($url);

        $reflection = new \ReflectionClass(get_class($this->client));
        $method = $reflection->getMethod('processResponse');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->client, array($response));
    }
}
