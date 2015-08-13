<?php

use DBorsatto\GiantBomb\Query;

class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    private $query;

    public function setUp()
    {
        $this->query = new Query();

        $this->query->addFilterBy('filter1', 'value1');
        $this->query->addFilterBy('filter2', 'value2');
        $this->query->addFilterBy('filter3', 'value3');

        $this->query->sortBy('sort4', 'desc');

        $this->query->setFieldList(array('field5', 'field6', 'field7'));

        $this->query->setParameter('parameter8', 'value8');

        $this->query->setResourceId('resource9');
    }

    public function testCompiledParameters()
    {
        $parameters = $this->query->compileParameters();

        $this->assertEquals($parameters['resource_id'], 'resource9');

        $this->assertEquals(count($parameters['query']), 4);

        $this->assertEquals(count($parameters['query']['filter_by']), 3);
        $this->assertEquals($parameters['query']['filter_by'], array(
            'filter1' => 'value1',
            'filter2' => 'value2',
            'filter3' => 'value3',
        ));

        $this->assertEquals($parameters['query']['sort_by'][0], 'sort4');
        $this->assertEquals($parameters['query']['sort_by'][1], 'desc');

        $this->assertEquals($parameters['query']['field_list'], array('field5', 'field6', 'field7'));

        $this->assertEquals($parameters['query']['parameter8'], 'value8');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindExceptionWhenNoRepositoryIsTied()
    {
        $this->query->find();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindOneExceptionWhenNoRepositoryIsTied()
    {
        $this->query->findOne();
    }
}
