<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;
use DBorsatto\GiantBomb\Client;

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

    public function testShortcuts()
    {
        $config = new Config('MyApiKey');
        $stubClient = $this->getMockBuilder('\DBorsatto\GiantBomb\Client')
            ->setConstructorArgs(array($config))
            ->getMock();
        $stubClient->method('loadResource')
            ->will($this->returnCallback(function ($url, $parameters, $type) {
                return array(
                    array('url' => $url),
                    array('parameters' => $parameters),
                    array('type' => $type),
                );
            }));

        $this->client->getRepository('Game')->setClient($stubClient);
        $model = $this->client->findOne('Game', 'resource_id');
        $this->assertTrue(is_array($model->getValues()));

        $this->client->getRepository('Search')->setClient($stubClient);
        $models = $this->client->search('value', 'resource');
        $this->assertEquals(count($models), 3);
        $this->assertEquals($models[2]->get('type'), 'collection');
    }
}
