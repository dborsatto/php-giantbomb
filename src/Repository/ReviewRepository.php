<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class ReviewRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Review';
    }

    public function getUrlSingle(): ?string
    {
        return 'review';
    }

    public function getUrlCollection(): ?string
    {
        return 'reviews';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'api_detail_url',
            'deck',
            'description',
            'dlc_name',
            'game',
            'platforms',
            'publish_date',
            'release',
            'reviewer',
            'score',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'deck',
            'description',
            'dlc_name',
            'game',
            'publish_date',
            'release',
            'reviewer',
            'score',
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
            'dlc_name',
            'game',
            'publish_date',
            'reviewer',
            'score',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'dlc_name',
            'game',
            'publish_date',
            'reviewer',
            'score',
        ];
    }
}
