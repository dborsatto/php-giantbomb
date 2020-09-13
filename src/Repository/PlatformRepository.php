<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class PlatformRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Platform';
    }

    public function getUrlSingle(): ?string
    {
        return 'platform';
    }

    public function getUrlCollection(): ?string
    {
        return 'platforms';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'abbreviation',
            'api_detail_url',
            'company',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'id',
            'image',
            'install_base',
            'name',
            'online_support',
            'original_price',
            'release_date',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'abbreviation',
            'api_detail_url',
            'company',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'id',
            'image',
            'install_base',
            'name',
            'online_support',
            'original_price',
            'release_date',
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
            'abbreviation',
            'company',
            'date_added',
            'date_last_updated',
            'id',
            'install_base',
            'name',
            'online_support',
            'original_price',
            'release_date',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'abbreviation',
            'date_added',
            'date_last_updated',
            'id',
            'install_base',
            'name',
            'online_support',
            'original_price',
            'release_date',
        ];
    }
}
