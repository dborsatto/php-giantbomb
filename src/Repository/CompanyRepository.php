<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class CompanyRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Company';
    }

    public function getUrlSingle(): ?string
    {
        return 'company';
    }

    public function getUrlCollection(): ?string
    {
        return 'companies';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'abbreviation',
            'aliases',
            'api_detail_url',
            'characters',
            'concepts',
            'date_added',
            'date_founded',
            'date_last_updated',
            'deck',
            'description',
            'developed_games',
            'developer_releases',
            'distributor_releases',
            'id',
            'image',
            'location_address',
            'location_city',
            'location_country',
            'location_state',
            'locations',
            'name',
            'objects',
            'people',
            'phone',
            'published_games',
            'publisher_releases',
            'site_detail_url',
            'website',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'abbreviation',
            'aliases',
            'api_detail_url',
            'date_added',
            'date_founded',
            'date_last_updated',
            'deck',
            'description',
            'id',
            'image',
            'location_address',
            'location_city',
            'location_country',
            'location_state',
            'name',
            'phone',
            'site_detail_url',
            'website',
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
            'abbreviation',
            'date_added',
            'date_founded',
            'date_last_updated',
            'id',
            'location_city',
            'location_country',
            'location_state',
            'name',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'abbreviation',
            'date_added',
            'date_founded',
            'date_last_updated',
            'id',
            'location_city',
            'location_country',
            'location_state',
            'name',
        ];
    }
}
