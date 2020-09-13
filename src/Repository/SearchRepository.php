<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class SearchRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Search';
    }

    public function getUrlSingle(): ?string
    {
        return null;
    }

    public function getUrlCollection(): ?string
    {
        return 'search';
    }

    public function requiresResourceID(): bool
    {
        return false;
    }

    protected function getFieldsInSingle(): array
    {
        return [];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'resource_type',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [
            'field_list',
            'limit',
            'page',
            'query',
            'resources',
            'subscriber_only',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [];
    }

    protected function getSortableFields(): array
    {
        return [];
    }
}
