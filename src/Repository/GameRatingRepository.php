<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class GameRatingRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'GameRating';
    }

    public function getUrlSingle(): ?string
    {
        return 'game_rating';
    }

    public function getUrlCollection(): ?string
    {
        return 'game_ratings';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'api_detail_url',
            'id',
            'image',
            'name',
            'rating_board',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'id',
            'image',
            'name',
            'rating_board',
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
            'rating_board',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'name',
            'rating_board',
        ];
    }
}
