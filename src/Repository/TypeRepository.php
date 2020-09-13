<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class TypeRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Type';
    }

    public function getUrlSingle(): ?string
    {
        return null;
    }

    public function getUrlCollection(): ?string
    {
        return 'types';
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
            'detail_resource_name',
            'id',
            'list_resource_name',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [];
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
