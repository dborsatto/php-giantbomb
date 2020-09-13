<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class VideoTypeRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'VideoType';
    }

    public function getUrlSingle(): ?string
    {
        return 'video_type';
    }

    public function getUrlCollection(): ?string
    {
        return 'video_types';
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
            'id',
            'name',
            'site_detail_url',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'deck',
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
        ];
    }

    protected function getFilterableFields(): array
    {
        return [];
    }

    protected function getSortableFields(): array
    {
        return [];
    }
}
