<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Exception;

use DBorsatto\GiantBomb\Query\FilterBy;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\Query\SortBy;
use DBorsatto\GiantBomb\RepositoryInterface;
use function implode;
use function sprintf;

class CompiledQueryException extends SdkException
{
    public static function invalidQueryParameter(Parameter $parameter, RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Parameter "%s" is not a valid query parameters for repository "%s"',
            $parameter->getField(),
            $configuration->getName()
        ));
    }

    public static function invalidFilteringParameter(FilterBy $filterBy, RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Parameter "%s" is not available for filtering in repository "%s"',
            $filterBy->getField(),
            $configuration->getName()
        ));
    }

    public static function invalidSortingParameter(SortBy $sortBy, RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Parameter "%s" is not available for sorting in repository "%s"',
            $sortBy->getField(),
            $configuration->getName()
        ));
    }

    /**
     * @param array<string>       $values
     * @param RepositoryInterface $configuration
     *
     * @return self
     */
    public static function invalidFieldListValue(
        array $values,
        RepositoryInterface $configuration
    ): self {
        return new self(sprintf(
            'Fields "%s" are not available in the field list for repository "%s"',
            implode(', ', $values),
            $configuration->getName()
        ));
    }

    public static function singleQueryOnCollectionRepository(RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Trying to perform a single-type query on repository "%s", which supports only collection type',
            $configuration->getName()
        ));
    }

    public static function collectionQueryOnSingleRepository(RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Trying to perform a collection-type query on repository "%s", which supports only single type',
            $configuration->getName()
        ));
    }

    public static function resourceIDNotSupported(RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Trying to query with a resource ID, but the repository "%s" does not provide support for them',
            $configuration->getName()
        ));
    }

    public static function missingResourceID(RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Trying to query a single element without providing a resource ID for repository "%s"',
            $configuration->getName()
        ));
    }

    public static function resourceIDOnCollectionRepository(RepositoryInterface $configuration): self
    {
        return new self(sprintf(
            'Trying to query a collection element by providing a resource ID for repository %s',
            $configuration->getName()
        ));
    }
}
