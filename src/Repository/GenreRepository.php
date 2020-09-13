<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class GenreRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Genre';
    }

    public function getUrlSingle(): ?string
    {
        return 'genre';
    }

    public function getUrlCollection(): ?string
    {
        return 'genres';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'api_detail_url',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'id',
            'image',
            'name',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'id',
            'image',
            'name',
            'site_detail_url',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [
            'field_list',
            'limit',
            'offset',
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
