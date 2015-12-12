<?php

/**
 * This file is part of the GiantBomb PHP API created by Davide Borsatto.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2015, Davide Borsatto
 */
namespace DBorsatto\GiantBomb;

/**
 * Class Model.
 *
 * @author Davide Borsatto <davide.borsatto@gmail.com>
 */
class Model
{
    /**
     * The model name.
     *
     * @var string
     */
    protected $name = null;

    /**
     * The model values.
     *
     * @var array
     */
    protected $values = array();

    /**
     * Class constructor.
     *
     * @param string $name
     * @param array  $values
     */
    public function __construct($name, $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * Returns all model values.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Returns a single value.
     *
     * @param string $value
     *
     * @return mixed
     */
    public function get($value)
    {
        if (!$this->has($value)) {
            throw new \InvalidArgumentException(sprintf(
                'Value %s is not a valid key, expecting one of %s',
                $value,
                implode(', ', array_keys($this->values))
            ));
        }

        return $this->values[$value];
    }

    /**
     * Magic function to allow methods like $model->getName().
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (0 === strpos($name, 'get')) {
            $key = $this->convertValueString(substr($name, 3));

            return $this->get($key);
        }

        throw new \InvalidArgumentException(sprintf('Call to invalid function %s on model %s', $name, $this->name));
    }

    /**
     * Magic function to access a model value.
     *
     * @param string $value
     *
     * @return mixed
     */
    public function __get($value)
    {
        return $this->get($value);
    }

    /**
     * Checks if the requested value is valid.
     *
     * @param string $value
     *
     * @return bool
     */
    public function has($value)
    {
        return array_key_exists($value, $this->values);
    }

    /**
     * Converts a value from CamelCase to pascal_case.
     *
     * @param string $value
     *
     * @return string
     */
    protected function convertValueString($value)
    {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $value));
    }
}
