<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class PromoRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Promo';
    }

    public function getUrlSingle(): ?string
    {
        return 'promo';
    }

    public function getUrlCollection(): ?string
    {
        return 'promos';
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
            'deck',
            'id',
            'image',
            'link',
            'name',
            'resource_type',
            'user',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'date_added',
            'deck',
            'id',
            'image',
            'link',
            'name',
            'resource_type',
            'user',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [
            'field_list',
            'limit',
            'offset',
            'sort',
            'filter',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'date_added',
            'id',
            'name',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'date_added',
            'id',
            'name',
        ];
    }
}
