<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class FranchiseRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Franchise';
    }

    public function getUrlSingle(): ?string
    {
        return 'franchise';
    }

    public function getUrlCollection(): ?string
    {
        return 'franchises';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'aliases',
            'api_detail_url',
            'characters',
            'concepts',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'games',
            'id',
            'image',
            'locations',
            'name',
            'objects',
            'people',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'aliases',
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
