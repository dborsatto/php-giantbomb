<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Repository;

class UserReviewRepository extends AbstractRepository
{
    public function getName(): string
    {
        return 'UserReview';
    }

    public function getUrlSingle(): ?string
    {
        return 'user_review';
    }

    public function getUrlCollection(): ?string
    {
        return 'user_reviews';
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
            'game',
            'reviewer',
            'score',
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
            'game',
            'reviewer',
            'score',
            'site_detail_url',
        ];
    }

    protected function getQueryParameters(): array
    {
        return [
            'field_list',
            'game',
            'limit',
            'offset',
            'sort',
            'filter',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'date_added',
            'date_last_updated',
            'game',
            'reviewer',
            'score',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'date_added',
            'date_last_updated',
            'reviewer',
            'score',
        ];
    }
}
