<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\ModelException;
use function array_key_exists;
use function array_keys;
use function mb_strpos;
use function mb_strtolower;
use function mb_substr;
use function preg_replace;

class Model
{
    protected string $name;

    /**
     * @var array<string, mixed>
     */
    protected array $values = [];

    /**
     * @param string               $name
     * @param array<string, mixed> $values
     */
    public function __construct(string $name, array $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * Magic function to allow methods like $model->getName().
     *
     * @param string $name
     * @param array  $arguments
     *
     * @throws ModelException
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (0 === mb_strpos($name, 'get')) {
            $key = $this->convertFromCamelCaseToPascalCase(mb_substr($name, 3));

            return $this->get($key);
        }

        throw ModelException::invalidMagicGetter($name, $this->name);
    }

    /**
     * @param string $value
     *
     * @throws ModelException
     *
     * @return mixed
     */
    public function __get(string $value)
    {
        return $this->get($value);
    }

    public function __isset(string $value): bool
    {
        return isset($this->values[$value]);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param string $value
     *
     * @throws ModelException
     *
     * @return mixed
     */
    public function get(string $value)
    {
        if (!$this->has($value)) {
            throw ModelException::invalidValue($value, array_keys($this->values));
        }

        return $this->values[$value];
    }

    public function has(string $value): bool
    {
        return array_key_exists($value, $this->values);
    }

    protected function convertFromCamelCaseToPascalCase(string $value): string
    {
        return mb_strtolower(
            preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $value)
        );
    }
}
