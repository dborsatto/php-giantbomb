<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Query;

class SortBy
{
    private const DIRECTION_ASC = 'asc';
    private const DIRECTION_DESC = 'desc';

    private string $field;
    private string $direction;

    private function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    public static function createAscending(string $field): self
    {
        return new self($field, self::DIRECTION_ASC);
    }

    public static function createDescending(string $field): self
    {
        return new self($field, self::DIRECTION_DESC);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function toUrlParameter(): string
    {
        return $this->field . ':' . $this->direction;
    }
}
