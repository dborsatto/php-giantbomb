<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class GameRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Game';
    }

    public function getUrlSingle(): ?string
    {
        return 'game';
    }

    public function getUrlCollection(): ?string
    {
        return 'games';
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
            'developers',
            'expected_release_day',
            'expected_release_month',
            'expected_release_quarter',
            'expected_release_year',
            'first_appearance_characters',
            'first_appearance_concepts',
            'first_appearance_locations',
            'first_appearance_objects',
            'first_appearance_people',
            'franchises',
            'genres',
            'id',
            'image',
            'images',
            'killed_characters',
            'locations',
            'name',
            'number_of_user_reviews',
            'objects',
            'original_game_rating',
            'original_release_date',
            'people',
            'platforms',
            'publishers',
            'releases',
            'reviews',
            'similar_games',
            'site_detail_url',
            'themes',
            'videos',
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
            'expected_release_month',
            'expected_release_quarter',
            'expected_release_year',
            'id',
            'image',
            'name',
            'number_of_user_reviews',
            'original_game_rating',
            'original_release_date',
            'platforms',
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
            'aliases',
            'date_added',
            'date_last_updated',
            'expected_release_month',
            'expected_release_quarter',
            'expected_release_year',
            'id',
            'name',
            'number_of_user_reviews',
            'original_release_date',
            'platforms',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'date_added',
            'date_last_updated',
            'id',
            'name',
            'number_of_user_reviews',
            'original_game_rating',
            'original_release_date',
        ];
    }
}
