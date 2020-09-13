<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class CharacterRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Character';
    }

    public function getUrlSingle(): ?string
    {
        return 'character';
    }

    public function getUrlCollection(): ?string
    {
        return 'characters';
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
            'birthday',
            'concepts',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'enemies',
            'first_appeared_in_game',
            'franchises',
            'friends',
            'games',
            'gender',
            'id',
            'image',
            'last_name',
            'locations',
            'name',
            'objects',
            'people',
            'real_name',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'aliases',
            'api_detail_url',
            'birthday',
            'date_added',
            'date_last_updated',
            'deck',
            'description',
            'first_appeared_in_game',
            'gender',
            'id',
            'image',
            'last_name',
            'name',
            'real_name',
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
            'birthday',
            'date_added',
            'date_last_updated',
            'gender',
            'id',
            'name',
            'real_name',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'birthday',
            'date_added',
            'date_last_updated',
            'gender',
            'id',
            'name',
            'real_name',
        ];
    }
}
