<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Config;
use DBorsatto\GiantBomb\Query;
use DBorsatto\GiantBomb\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
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
            ->setConstructorArgs([$config, null])
            ->getMock();
        $stubClient->method('loadResource')
            ->will($this->returnCallback(function ($url, $parameters) {
                return [
                    ['url' => $url],
                    ['parameters' => $parameters],
                ];
            }));

        return new Repository($stubClient, $model, $repoConfig);
    }

    public function testReturnValues()
    {
        $models = $this->repository->query()->find();
        $this->assertSame(\count($models), 2);
        $this->assertInternalType('array', $models[1]->get('parameters'));

        $models = $this->repository->find(new Query());
        $this->assertSame(\count($models), 2);
        $this->assertInternalType('array', $models[1]->get('parameters'));

        $query = new Query();
        $query->addFilterBy('name', 'name1');
        $query->sortBy('name', 'desc');
        $query->setFieldList(['id', 'name', 'description']);
        $query->setParameter('platforms', 'ps3');

        $models = $this->repository->find($query);
        $this->assertSame(\count($models), 2);
        $this->assertInternalType('array', $models[1]->get('parameters'));

        $query = new Query();
        $query->setResourceId('id');
        $model = $this->repository->findOne($query);
        $this->assertInternalType('array', $model->getValues());

        $query = new Query();
        $query->setResourceId('id');
        $model = $query->findOne($this->repository);
        $this->assertInternalType('array', $model->getValues());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFilter()
    {
        $query = new Query();
        $query->addFilterBy('invalid', '');
        $this->repository->find($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSort()
    {
        $query = new Query();
        $query->sortBy('invalid');
        $this->repository->find($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidField()
    {
        $query = new Query();
        $query->setFieldList(['invalid']);
        $this->repository->find($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidParameterName()
    {
        $query = new Query();
        $query->setParameter('invalid', '');
        $this->repository->find($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResourceIdPresenceNotAllowedByQuery()
    {
        $query = new Query();
        $query->setResourceId('invalid');
        $this->repository->find($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResourceIdPresenceNotAllowedByRepository()
    {
        $repoApiConfig = [
            'api_endpoint' => '',
            'repositories' => ['Game' => [
                'url_single' => 'url',
                'url_collection' => 'url',
                'resource_id' => false,
            ]],
        ];
        $config = new Config('MyApiKey', $repoApiConfig);
        $query = new Query();
        $query->setResourceId('invalid');
        $this->createRepository($config)->find($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResourceIdAbsence()
    {
        $query = new Query();
        $this->repository->findOne($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidQueryToRepositoryWithoutUrlSingle()
    {
        $repoApiConfig = [
            'api_endpoint' => '',
            'repositories' => ['Game' => [
                'url_single' => null,
                'url_collection' => 'url',
            ]],
        ];
        $config = new Config('MyApiKey', $repoApiConfig);
        $this->createRepository($config)->findOne(new Query());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidQueryToRepositoryWithoutUrlCollection()
    {
        $repoApiConfig = [
            'api_endpoint' => '',
            'repositories' => ['Game' => [
                'url_single' => 'url',
                'url_collection' => null,
            ]],
        ];
        $config = new Config('MyApiKey', $repoApiConfig);
        $this->createRepository($config)->find(new Query());
    }
}
