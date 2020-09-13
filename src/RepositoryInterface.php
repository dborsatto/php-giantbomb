<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Query\FilterBy;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\Query\SortBy;

interface RepositoryInterface
{
    public function getName(): string;

    /**
     * The URL for the resource with a single result element.
     *
     * @return string|null
     */
    public function getUrlSingle(): ?string;

    /**
     * The URL for the resource with a collection result element.
     *
     * @return string|null
     */
    public function getUrlCollection(): ?string;

    /**
     * @param array<string> $fields
     *
     * @return bool
     */
    public function canSelectSingle(array $fields): bool;

    /**
     * @param array<string> $fields
     *
     * @return bool
     */
    public function canSelectCollection(array $fields): bool;

    public function supportsQueryParameter(Parameter $parameter): bool;

    public function supportsFilterBy(FilterBy $filterBy): bool;

    public function supportsSortBy(SortBy $sortBy): bool;

    public function requiresResourceID(): bool;
}
