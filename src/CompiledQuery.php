<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\CompiledQueryException;
use DBorsatto\GiantBomb\Query\FilterBy;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\Query\SortBy;
use function http_build_query;
use function implode;
use function mb_substr;
use function sha1;
use function sprintf;

class CompiledQuery
{
    private const ENDPOINT_COLLECTION = 'collection';
    private const ENDPOINT_SINGLE = 'single';

    private string $url;

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     * @param string              $type
     *
     * @throws CompiledQueryException
     */
    private function __construct(RepositoryInterface $configuration, Query $query, string $type)
    {
        $this->url = $this->buildUrl($configuration, $query, $type);
        $this->parameters = $this->buildQueryParameters($configuration, $query, $type);
    }

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     *
     * @throws CompiledQueryException
     *
     * @return self
     */
    public static function createForSingle(RepositoryInterface $configuration, Query $query): self
    {
        return new self($configuration, $query, self::ENDPOINT_SINGLE);
    }

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     *
     * @throws CompiledQueryException
     *
     * @return self
     */
    public static function createForCollection(RepositoryInterface $configuration, Query $query): self
    {
        return new self($configuration, $query, self::ENDPOINT_COLLECTION);
    }

    public function getQueryUrl(string $apiKey = null): string
    {
        $parameters = $this->parameters;
        $parameters['format'] = 'json';
        if (null !== $apiKey) {
            $parameters['api_key'] = $apiKey;
        }

        return $this->url . '?' . http_build_query($parameters);
    }

    public function getSignature(): string
    {
        return 'giantbomb_' . mb_substr(sha1($this->getQueryUrl()), 0, 7);
    }

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     * @param string              $type
     *
     * @throws CompiledQueryException
     *
     * @return string
     */
    private function buildUrl(RepositoryInterface $configuration, Query $query, string $type): string
    {
        if (self::ENDPOINT_SINGLE === $type) {
            return $this->buildUrlSingle($configuration, $query);
        }

        return $this->buildUrlCollection($configuration, $query);
    }

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     *
     * @throws CompiledQueryException
     *
     * @return string
     */
    private function buildUrlSingle(RepositoryInterface $configuration, Query $query): string
    {
        $urlSingle = $configuration->getUrlSingle();
        if (null === $urlSingle) {
            throw CompiledQueryException::singleQueryOnCollectionRepository($configuration);
        }

        $resourceID = $query->getResourceID();
        if (!$configuration->requiresResourceID() && $resourceID) {
            throw CompiledQueryException::resourceIDNotSupported($configuration);
        }

        if (null === $resourceID) {
            throw CompiledQueryException::missingResourceID($configuration);
        }

        return sprintf('%s/%s/', $urlSingle, $resourceID);
    }

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     *
     * @throws CompiledQueryException
     *
     * @return string
     */
    private function buildUrlCollection(RepositoryInterface $configuration, Query $query): string
    {
        $urlCollection = $configuration->getUrlCollection();
        if (null === $urlCollection) {
            throw CompiledQueryException::collectionQueryOnSingleRepository($configuration);
        }

        $resourceID = $query->getResourceID();
        if (null !== $resourceID) {
            throw CompiledQueryException::resourceIDOnCollectionRepository($configuration);
        }

        return sprintf('%s/', $urlCollection);
    }

    /**
     * @param RepositoryInterface $configuration
     * @param Query               $query
     * @param string              $type
     *
     * @throws CompiledQueryException
     *
     * @return array<string, string>
     */
    private function buildQueryParameters(
        RepositoryInterface $configuration,
        Query $query,
        string $type
    ): array {
        $parameters = [];

        // The parameter is a filter,
        // so it must be checked if it is whitelisted
        $filterBy = $query->getFilterBy();
        if ($filterBy) {
            $parameters['filter'] = $this->queryAddFilterByParameter($configuration, $filterBy);
        }

        // The parameter is a sorting field,
        // so it must be checked if it is whitelisted
        $sortBy = $query->getSortBy();
        if ($sortBy) {
            $parameters['sort_by'] = $this->queryAddSortByParameter($configuration, $sortBy);
        }

        // The parameter is a list of fields,
        // so it must be checked whether they are supported by the repository
        $fieldList = $query->getFieldList();
        if ($fieldList) {
            $parameters['field_list'] = $this->queryAddFieldList($configuration, $type, $fieldList);
        }

        /** @var Parameter $parameter */
        foreach ($query->getParameters() as $parameter) {
            // The parameter is of any other kind,
            // so it must be checked that it is supported by the Repository
            if (!$configuration->supportsQueryParameter($parameter)) {
                throw CompiledQueryException::invalidQueryParameter($parameter, $configuration);
            }

            $parameters[$parameter->getField()] = $parameter->getValue();
        }

        return $parameters;
    }

    /**
     * Checks if the parameters are whitelisted and creates the URL string.
     *
     * @param RepositoryInterface $configuration
     * @param array<FilterBy>     $values
     *
     * @throws CompiledQueryException
     *
     * @return string
     */
    private function queryAddFilterByParameter(RepositoryInterface $configuration, array $values): string
    {
        $filters = [];
        /** @var FilterBy $filterBy */
        foreach ($values as $filterBy) {
            if (!$configuration->supportsFilterBy($filterBy)) {
                throw CompiledQueryException::invalidFilteringParameter($filterBy, $configuration);
            }

            $filters[] = $filterBy->toUrlParameter();
        }

        return implode(',', $filters);
    }

    /**
     * Checks if the field is sortable and creates the URL string.
     *
     * @param RepositoryInterface $configuration
     * @param SortBy              $sortBy
     *
     * @throws CompiledQueryException
     *
     * @return string
     */
    private function queryAddSortByParameter(RepositoryInterface $configuration, SortBy $sortBy): string
    {
        if (!$configuration->supportsSortBy($sortBy)) {
            throw CompiledQueryException::invalidSortingParameter($sortBy, $configuration);
        }

        return $sortBy->toUrlParameter();
    }

    /**
     * Checks if the values are in the repository's field list and creates the URL string.
     *
     * @param RepositoryInterface $configuration
     * @param string              $type
     * @param array<string>       $values
     *
     * @throws CompiledQueryException
     *
     * @return string
     */
    private function queryAddFieldList(RepositoryInterface $configuration, string $type, array $values): string
    {
        if (self::ENDPOINT_SINGLE === $type) {
            if (!$configuration->canSelectSingle($values)) {
                throw CompiledQueryException::invalidFieldListValue($values, $configuration);
            }
        } else {
            if (!$configuration->canSelectCollection($values)) {
                throw CompiledQueryException::invalidFieldListValue($values, $configuration);
            }
        }

        return implode(',', $values);
    }
}
