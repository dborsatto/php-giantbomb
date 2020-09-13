<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class ReleaseRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Release';
    }

    public function getUrlSingle(): ?string
    {
        return 'release';
    }

    public function getUrlCollection(): ?string
    {
        return 'releases';
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
            'developers',
            'expected_release_day',
            'expected_release_month',
            'expected_release_quarter',
            'expected_release_year',
            'game',
            'game_rating',
            'id',
            'image',
            'images',
            'name',
            'platform',
            'product_code_type',
            'product_code_value',
            'publishers',
            'region',
            'release_date',
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
            'expected_release_day',
            'expected_release_month',
            'expected_release_quarter',
            'expected_release_year',
            'game',
            'game_rating',
            'id',
            'image',
            'name',
            'platform',
            'product_code_type',
            'product_code_value',
            'region',
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
            'platforms',
            'sort',
            'filter',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'date_added',
            'date_last_updated',
            'expected_release_month',
            'expected_release_quarter',
            'expected_release_year',
            'game',
            'game_rating',
            'id',
            'name',
            'platform',
            'product_code_type',
            'product_code_value',
            'region',
            'release_date',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'date_added',
            'date_last_updated',
            'game',
            'game_rating',
            'id',
            'name',
            'platform',
            'product_code_type',
            'product_code_value',
            'region',
            'release_date',
        ];
    }
}
