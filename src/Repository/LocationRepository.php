<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class LocationRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Location';
    }

    public function getUrlSingle(): ?string
    {
        return 'location';
    }

    public function getUrlCollection(): ?string
    {
        return 'locations';
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
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'first_appeared_in_game',
            'id',
            'image',
            'name',
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
            'first_appeared_in_game',
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
            'sort',
            'filter',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'aliases',
            'date_added',
            'date_last_updated',
            'id',
            'name',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'date_added',
            'date_last_updated',
            'id',
            'name',
        ];
    }
}
