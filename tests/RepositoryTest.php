<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;
use DBorsatto\GiantBomb\Query;
use DBorsatto\GiantBomb\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    private $repository = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $config = new Config('MyApiKey');
        $this->repository = $this->createRepository($config);
    }

    private function createRepository(Config $config)
    {
        $repositories = $config->getRepositories();
        $model = 'Game';
        $repoConfig = $repositories[$model];
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

        return new Repository($stubClient, $model, $repoConfig);
    }

    public function testReturnValues()
    {
        $models = $this->repository->query()->find();
        $this->assertEquals(count($models), 2);
        $this->assertTrue(is_array($models[1]->get('parameters')));

        $models = $this->repository->find(new Query());
        $this->assertEquals(count($models), 2);
        $this->assertTrue(is_array($models[1]->get('parameters')));

        $query = new Query();
        $query->addFilterBy('name', 'name1');
        $query->sortBy('name', 'desc');
        $query->setFieldList(array('id', 'name', 'description'));
        $query->setParameter('platforms', 'ps3');

        $models = $this->repository->find($query);
        $this->assertEquals(count($models), 2);
        $this->assertTrue(is_array($models[1]->get('parameters')));

        $query = new Query();
        $query->setResourceId('id');
        $model = $this->repository->findOne($query);
        $this->assertTrue(is_array($model->getValues()));

        $query = new Query();
        $query->setResourceId('id');
        $model = $query->findOne($this->repository);
        $this->assertTrue(is_array($model->getValues()));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFilter()
    {
        $query = new Query();
        $query->addFilterBy('invalid', '');
        $this->repository->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidSort()
    {
        $query = new Query();
        $query->sortBy('invalid');
        $this->repository->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidField()
    {
        $query = new Query();
        $query->setFieldList(array('invalid'));
        $this->repository->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidParameterName()
    {
        $query = new Query();
        $query->setParameter('invalid', '');
        $this->repository->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidResourceIdPresenceNotAllowedByQuery()
    {
        $query = new Query();
        $query->setResourceId('invalid');
        $this->repository->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidResourceIdPresenceNotAllowedByRepository()
    {
        $repoApiConfig = array(
            'api_endpoint' => '',
            'repositories' => array('Game' => array(
                'url_single' => 'url',
                'url_collection' => 'url',
                'resource_id' => false,
            )),
        );
        $config = new Config('MyApiKey', $repoApiConfig);
        $query = new Query();
        $query->setResourceId('invalid');
        $this->createRepository($config)->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidResourceIdAbsence()
    {
        $query = new Query();
        $this->repository->findOne($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidParameterValue()
    {
        $query = new Query();
        $query->setParameter('limit', array());
        $this->repository->find($query);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidQueryToRepositoryWithoutUrlSingle()
    {
        $repoApiConfig = array(
            'api_endpoint' => '',
            'repositories' => array('Game' => array(
                'url_single' => null,
                'url_collection' => 'url'
            )),
        );
        $config = new Config('MyApiKey', $repoApiConfig);
        $this->createRepository($config)->findOne(new Query());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidQueryToRepositoryWithoutUrlCollection()
    {
        $repoApiConfig = array(
            'api_endpoint' => '',
            'repositories' => array('Game' => array(
                'url_single' => 'url',
                'url_collection' => null
            )),
        );
        $config = new Config('MyApiKey', $repoApiConfig);
        $this->createRepository($config)->find(new Query());
    }
}
