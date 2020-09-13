<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class ThemeRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Theme';
    }

    public function getUrlSingle(): ?string
    {
        return 'theme';
    }

    public function getUrlCollection(): ?string
    {
        return 'themes';
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
            'name',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'id',
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
