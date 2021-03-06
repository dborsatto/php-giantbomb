<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class RatingBoardRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'RatingBoard';
    }

    public function getUrlSingle(): ?string
    {
        return 'rating_board';
    }

    public function getUrlCollection(): ?string
    {
        return 'rating_boards';
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
            'region',
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
            'sort',
            'filter',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'id',
            'name',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
