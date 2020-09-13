<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Query\FilterBy;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\Query\SortBy;

class Query
{
    /**
     * @var array<FilterBy>
     */
    private array $filterBy = [];

    private ?SortBy $sortBy = null;

    /**
     * @var array<string>
     */
    private array $fieldList = [];

    private ?string $resourceID = null;

    /**
     * @var array<Parameter>
     */
    private array $parameters = [];

    public static function create(): self
    {
        return new self();
    }

    public static function createWithParameter(string $parameter, string $value): self
    {
        $query = new self();
        $query->parameters[] = new Parameter($parameter, $value);

        return $query;
    }

    public static function createForResourceId(string $resourceId): self
    {
        $query = new self();
        $query->resourceID = $resourceId;

        return $query;
    }

    public function addFilterBy(string $field, string $value): self
    {
        $query = clone $this;
        $query->filterBy[] = new FilterBy($field, $value);

        return $query;
    }

    public function sortAscending(string $field): self
    {
        $query = clone $this;
        $query->sortBy = SortBy::createAscending($field);

        return $query;
    }

    public function sortDescending(string $field): self
    {
        $query = clone $this;
        $query->sortBy = SortBy::createDescending($field);

        return $query;
    }

    /**
     * @param array<string> $list
     *
     * @return Query
     */
    public function setFieldList(array $list): self
    {
        $query = clone $this;
        $query->fieldList = $list;

        return $query;
    }

    public function setParameter(string $parameter, string $value): self
    {
        $query = clone $this;
        $query->parameters[] = new Parameter($parameter, $value);

        return $query;
    }

    public function setResourceID(string $resourceID): self
    {
        $query = clone $this;
        $query->resourceID = $resourceID;

        return $query;
    }

    /**
     * @return array<FilterBy>
     */
    public function getFilterBy(): array
    {
        return $this->filterBy;
    }

    public function getSortBy(): ?SortBy
    {
        return $this->sortBy;
    }

    /**
     * @return array<string>
     */
    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    /**
     * @return string|null
     */
    public function getResourceID(): ?string
    {
        return $this->resourceID;
    }

    /**
     * @return array<Parameter>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
