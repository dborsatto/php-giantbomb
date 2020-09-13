<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class VideoRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Video';
    }

    public function getUrlSingle(): ?string
    {
        return 'video';
    }

    public function getUrlCollection(): ?string
    {
        return 'videos';
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
            'hd_url',
            'high_url',
            'id',
            'image',
            'length_seconds',
            'low_url',
            'name',
            'publish_date',
            'site_detail_url',
            'url',
            'user',
            'youtube_id',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'deck',
            'hd_url',
            'high_url',
            'id',
            'image',
            'length_seconds',
            'low_url',
            'name',
            'publish_date',
            'site_detail_url',
            'url',
            'user',
            'video_type',
            'youtube_id',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [
            'field_list',
            'limit',
            'offset',
            'sort',
            'subscriber_only',
            'video_type',
            'filter',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'id',
            'length_seconds',
            'name',
            'publish_date',
            'user',
            'video_type',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'length_seconds',
            'name',
            'publish_date',
            'user',
            'video_type',
        ];
    }
}
