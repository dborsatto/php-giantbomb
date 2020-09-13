<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace spec\DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\CompiledQueryException;
use DBorsatto\GiantBomb\Query;
use DBorsatto\GiantBomb\Query\FilterBy;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\Query\SortBy;
use DBorsatto\GiantBomb\RepositoryInterface;
use PhpSpec\ObjectBehavior;

class CompiledQuerySpec extends ObjectBehavior
{
    public function let(RepositoryInterface $repository): void
    {
        $repository->getName()
            ->willReturn('Game');
        $repository->getUrlSingle()
            ->willReturn('game');
        $repository->getUrlCollection()
            ->willReturn('games');
        $repository->requiresResourceID()
            ->willReturn(true);
    }

    public function it_creates_for_single(RepositoryInterface $repository): void
    {
        $query = Query::createForResourceId('resource_id');
        $this->beConstructedThrough('createForSingle', [$repository, $query]);

        $this->getQueryUrl('api_key')
            ->shouldBe('game/resource_id/?format=json&api_key=api_key');
    }

    public function it_throws_if_no_single_url(RepositoryInterface $repository): void
    {
        $query = Query::createForResourceId('resource_id');
        $this->beConstructedThrough('createForSingle', [$repository, $query]);

        $repository->getUrlSingle()
            ->willReturn(null);

        $this->shouldThrow(CompiledQueryException::singleQueryOnCollectionRepository($repository->getWrappedObject()))
            ->duringInstantiation();
    }

    public function it_throws_if_does_not_requires_resource_id_and_one_provided(RepositoryInterface $repository): void
    {
        $query = Query::createForResourceId('resource_id');
        $this->beConstructedThrough('createForSingle', [$repository, $query]);

        $repository->requiresResourceID()
            ->willReturn(false);

        $this->shouldThrow(CompiledQueryException::resourceIDNotSupported($repository->getWrappedObject()))
            ->duringInstantiation();
    }

    public function it_throws_if_requires_resource_id_and_none_provided(RepositoryInterface $repository): void
    {
        $query = new Query();
        $this->beConstructedThrough('createForSingle', [$repository, $query]);

        $this->shouldThrow(CompiledQueryException::missingResourceID($repository->getWrappedObject()))
            ->duringInstantiation();
    }

    public function it_creates_for_collection(RepositoryInterface $repository): void
    {
        $query = new Query();
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $this->getQueryUrl('api_key')
            ->shouldBe('games/?format=json&api_key=api_key');
    }

    public function it_throws_if_no_collection_url(RepositoryInterface $repository): void
    {
        $query = new Query();
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $repository->getUrlCollection()
            ->willReturn(null);

        $this->shouldThrow(CompiledQueryException::collectionQueryOnSingleRepository($repository->getWrappedObject()))
            ->duringInstantiation();
    }

    public function it_throws_if_collection_and_resource_id_provided(RepositoryInterface $repository): void
    {
        $query = Query::createForResourceId('resource_id');
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $this->shouldThrow(CompiledQueryException::resourceIDOnCollectionRepository($repository->getWrappedObject()))
            ->duringInstantiation();
    }

    public function it_creates_with_all_options(RepositoryInterface $repository): void
    {
        $query = Query::create()
            ->setParameter('parameter', 'value')
            ->sortAscending('name')
            ->addFilterBy('name', 'Uncharted')
            ->setFieldList(['name']);
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $repository->supportsFilterBy(new FilterBy('name', 'Uncharted'))
            ->willReturn(true);
        $repository->supportsSortBy(SortBy::createAscending('name'))
            ->willReturn(true);
        $repository->canSelectCollection(['name'])
            ->willReturn(true);
        $repository->supportsQueryParameter(new Parameter('parameter', 'value'))
            ->willReturn(true);

        $this->getQueryUrl('api_key')
            ->shouldBe('games/?filter=name%3AUncharted&sort_by=name%3Aasc&field_list=name&parameter=value&format=json&api_key=api_key');
    }

    public function it_throws_if_filter_not_supported(RepositoryInterface $repository): void
    {
        $query = Query::create()
            ->addFilterBy('name', 'Uncharted');
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $repository->supportsFilterBy(new FilterBy('name', 'Uncharted'))
            ->willReturn(false);

        $exception = CompiledQueryException::invalidFilteringParameter(
            new FilterBy('name', 'Uncharted'),
            $repository->getWrappedObject()
        );
        $this->shouldThrow($exception)
            ->duringInstantiation();
    }

    public function it_throws_if_sorting_not_supported(RepositoryInterface $repository): void
    {
        $query = Query::create()
            ->sortAscending('name');
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $repository->supportsSortBy(SortBy::createAscending('name'))
            ->willReturn(false);

        $exception = CompiledQueryException::invalidSortingParameter(
            SortBy::createAscending('name'),
            $repository->getWrappedObject()
        );
        $this->shouldThrow($exception)
            ->duringInstantiation();
    }

    public function it_throws_if_field_select_is_not_supported(RepositoryInterface $repository): void
    {
        $query = Query::create()
            ->setFieldList(['name']);
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $repository->canSelectCollection(['name'])
            ->willReturn(false);

        $exception = CompiledQueryException::invalidFieldListValue(
            ['name'],
            $repository->getWrappedObject()
        );
        $this->shouldThrow($exception)
            ->duringInstantiation();
    }

    public function it_throws_if_parameter_is_not_supported(RepositoryInterface $repository): void
    {
        $query = Query::create()
            ->setParameter('limit', '10');
        $this->beConstructedThrough('createForCollection', [$repository, $query]);

        $repository->supportsQueryParameter(new Parameter('limit', '10'))
            ->willReturn(false);

        $exception = CompiledQueryException::invalidQueryParameter(
            new Parameter('limit', '10'),
            $repository->getWrappedObject()
        );
        $this->shouldThrow($exception)
            ->duringInstantiation();
    }
}
