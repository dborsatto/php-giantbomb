<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class ChatRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'Chat';
    }

    public function getUrlSingle(): ?string
    {
        return 'chat';
    }

    public function getUrlCollection(): ?string
    {
        return 'chats';
    }

    public function requiresResourceID(): bool
    {
        return true;
    }

    protected function getFieldsInSingle(): array
    {
        return [
            'api_detail_url',
            'channel_name',
            'deck',
            'image',
            'password',
            'site_detail_url',
            'title',
        ];
    }

    protected function getFieldsInCollection(): array
    {
        return [
            'api_detail_url',
            'channel_name',
            'deck',
            'image',
            'password',
            'site_detail_url',
            'title',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [
            'field_list',
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
