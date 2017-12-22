<?php

namespace DBorsatto\GiantBomb\Test;

use DBorsatto\GiantBomb\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    private $query;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->query = new Query();

        $this->query->addFilterBy('filter1', 'value1');
        $this->query->addFilterBy('filter2', 'value2');
        $this->query->addFilterBy('filter3', 'value3');

        $this->query->sortBy('sort4', 'desc');

        $this->query->setFieldList(['field5', 'field6', 'field7']);

        $this->query->setParameter('parameter8', 'value8');

        $this->query->setResourceId('resource9');
    }

    public function testCompiledParameters()
    {
        $parameters = $this->query->compileParameters();

        $this->assertSame($parameters['resource_id'], 'resource9');

        $this->assertSame(\count($parameters['query']), 4);

        $this->assertSame(\count($parameters['query']['filter_by']), 3);
        $this->assertSame($parameters['query']['filter_by'], [
            'filter1' => 'value1',
            'filter2' => 'value2',
            'filter3' => 'value3',
        ]);

        $this->assertSame($parameters['query']['sort_by'][0], 'sort4');
        $this->assertSame($parameters['query']['sort_by'][1], 'desc');

        $this->assertSame($parameters['query']['field_list'], ['field5', 'field6', 'field7']);

        $this->assertSame($parameters['query']['parameter8'], 'value8');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFindExceptionWhenNoRepositoryIsTied()
    {
        $this->query->find();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFindOneExceptionWhenNoRepositoryIsTied()
    {
        $this->query->findOne();
    }
}
