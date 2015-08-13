<?php

use DBorsatto\GiantBomb\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    private $model = null;

    private $testValues = array(
        'key1' => 'value2',
        'key3' => array('value4', 'value5'),
        'key6' => 7,
        'key8' => null,
    );

    public function setUp()
    {
        $this->model = new Model('TestModel', $this->testValues);
    }

    public function testValues()
    {
        $this->assertEquals($this->model->getValues(), $this->testValues);
    }

    public function testValidValues()
    {
        $this->assertEquals($this->model->get('key1'), 'value2');
        $this->assertEquals($this->model->get('key3'), array('value4', 'value5'));
        $this->assertEquals($this->model->get('key6'), 7);
        $this->assertEquals($this->model->get('key8'), null);
        $this->assertEquals($this->model->get('key8', 'SomeDefaultValue'), 'SomeDefaultValue');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionWithInvalidValue()
    {
        $this->model->get('invalid');
    }

    public function testMagicFunctionWithValidValues()
    {
        $this->assertEquals($this->model->key1, 'value2');
        $this->assertEquals($this->model->key3, array('value4', 'value5'));
        $this->assertEquals($this->model->key6, 7);
        $this->assertEquals($this->model->key8, null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMagicFunctionWithInvalidValues()
    {
        $this->model->invalid;
    }

    public function testMagicGetters()
    {
        $this->assertEquals($this->model->getKey1(), 'value2');
        $this->assertEquals($this->model->getKey3(), array('value4', 'value5'));
        $this->assertEquals($this->model->getKey6(), 7);
        $this->assertEquals($this->model->getKey8(), null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMagicGettersThrowExceptionWithInvalidParameter()
    {
        $this->model->invalidFunction();
    }
}
