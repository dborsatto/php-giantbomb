<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

use DBorsatto\GiantBomb\Query\FilterBy;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\Query\SortBy;
use DBorsatto\GiantBomb\RepositoryInterface;
use function in_array;

abstract class AbstractRepository implements RepositoryInterface
{
    public function canSelectSingle(array $fields): bool
    {
        return $this->canSelect($fields, $this->getFieldsInSingle());
    }

    public function canSelectCollection(array $fields): bool
    {
        return $this->canSelect($fields, $this->getFieldsInCollection());
    }

    public function supportsQueryParameter(Parameter $parameter): bool
    {
        return in_array($parameter->getField(), $this->getQueryParameters(), true);
    }

    public function supportsFilterBy(FilterBy $filterBy): bool
    {
        return in_array($filterBy->getField(), $this->getFilterableFields(), true);
    }

    public function supportsSortBy(SortBy $sortBy): bool
    {
        return in_array($sortBy->getField(), $this->getSortableFields(), true);
    }

    /**
     * @return array<string>
     */
    abstract protected function getFieldsInSingle(): array;

    /**
     * @return array<string>
     */
    abstract protected function getFieldsInCollection(): array;

    /**
     * @return array<string>
     */
    abstract protected function getQueryParameters(): array;

    /**
     * @return array<string>
     */
    abstract protected function getFilterableFields(): array;

    /**
     * @return array<string>
     */
    abstract protected function getSortableFields(): array;

    /**
     * @param array<string> $fields
     * @param array<string> $supported
     *
     * @return bool
     */
    private function canSelect(array $fields, array $supported): bool
    {
        foreach ($fields as $field) {
            if (in_array($field, $supported, true)) {
                return false;
            }
        }

        return true;
    }
}
