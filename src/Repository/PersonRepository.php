<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class PersonRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Person';
    }

    public function getUrlSingle(): ?string
    {
        return 'person';
    }

    public function getUrlCollection(): ?string
    {
        return 'people';
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
            'birth_date',
            'characters',
            'concepts',
            'country',
            'date_added',
            'date_last_updated',
            'death_date',
            'deck',
            'description',
            'first_credited_game',
            'franchises',
            'games',
            'gender',
            'hometown',
            'id',
            'image',
            'locations',
            'name',
            'objects',
            'people',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'aliases',
            'api_detail_url',
            'birth_date',
            'country',
            'date_added',
            'date_last_updated',
            'death_date',
            'deck',
            'description',
            'first_credited_game',
            'gender',
            'hometown',
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
            'birth_date',
            'country',
            'date_added',
            'date_last_updated',
            'death_date',
            'gender',
            'hometown',
            'id',
            'name',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'birth_date',
            'country',
            'date_added',
            'date_last_updated',
            'death_date',
            'gender',
            'hometown',
            'id',
            'name',
        ];
    }
}
