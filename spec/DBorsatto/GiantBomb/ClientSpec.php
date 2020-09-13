<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace spec\DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\ApiCaller;
use DBorsatto\GiantBomb\CompiledQuery;
use DBorsatto\GiantBomb\Configuration;
use DBorsatto\GiantBomb\Model;
use DBorsatto\GiantBomb\Query;
use DBorsatto\GiantBomb\Query\Parameter;
use DBorsatto\GiantBomb\RepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\SimpleCache\CacheInterface;

class ClientSpec extends ObjectBehavior
{
    public function let(
        Configuration $configuration,
        CacheInterface $cache,
        ApiCaller $apiCaller,
        RepositoryInterface $repository
    ): void {
        $this->beConstructedWith($configuration, $cache, $apiCaller);

        $configuration->getApiKey()
            ->willReturn('api_key');
        $configuration->getApiEndpoint()
            ->willReturn('https://www.example.com');
        $configuration->getRepository(Argument::any())
            ->willReturn($repository);

        $repository->getName()
            ->willReturn('Game');
        $repository->getUrlSingle()
            ->willReturn('game');
        $repository->getUrlCollection()
            ->willReturn('games');
        $repository->requiresResourceID()
            ->willReturn(true);
        $repository->supportsQueryParameter(new Parameter('query', 'Value'))
            ->willReturn(true);
    }

    public function it_finds_without_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = [
            ['name' => 'Value 1'],
            ['name' => 'Value 2'],
        ];
        $expected = [
            new Model('Game', $values[0]),
            new Model('Game', $values[1]),
        ];

        $apiCaller->fetch('https://www.example.com', 'api_key', Argument::type(CompiledQuery::class))
            ->shouldBeCalled()
            ->willReturn($values);

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);
        $cache->set(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $this->find('Game', Query::create())
            ->shouldBeLike($expected);
    }

    public function it_finds_with_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = [
            ['name' => 'Value 1'],
            ['name' => 'Value 2'],
        ];
        $expected = [
            new Model('Game', $values[0]),
            new Model('Game', $values[1]),
        ];

        $apiCaller->fetch(Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);
        $cache->get(Argument::any())
            ->shouldBeCalled()
            ->willReturn($values);
        $cache->set(Argument::any())
            ->shouldNotBeCalled();

        $this->find('Game', Query::create())
            ->shouldBeLike($expected);
    }

    public function it_finds_one_without_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = ['name' => 'Value 1'];
        $expected = new Model('Game', $values);

        $apiCaller->fetch('https://www.example.com', 'api_key', Argument::type(CompiledQuery::class))
            ->shouldBeCalled()
            ->willReturn($values);

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);
        $cache->set(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $this->findOne('Game', Query::createForResourceId('resource_id'))
            ->shouldBeLike($expected);
    }

    public function it_finds_one_with_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = ['name' => 'Value 1'];
        $expected = new Model('Game', $values);

        $apiCaller->fetch(Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);
        $cache->get(Argument::any())
            ->shouldBeCalled()
            ->willReturn($values);
        $cache->set(Argument::any())
            ->shouldNotBeCalled();

        $this->findOne('Game', Query::createForResourceId('resource_id'))
            ->shouldBeLike($expected);
    }

    public function it_shortcuts_for_resource_id_without_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = ['name' => 'Value 1'];
        $expected = new Model('Game', $values);

        $apiCaller->fetch('https://www.example.com', 'api_key', Argument::type(CompiledQuery::class))
            ->shouldBeCalled()
            ->willReturn($values);

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);
        $cache->set(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $this->findWithResourceID('Game', 'resource_id')
            ->shouldBeLike($expected);
    }

    public function it_shortcuts_for_resource_id_with_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = ['name' => 'Value 1'];
        $expected = new Model('Game', $values);

        $apiCaller->fetch(Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);
        $cache->get(Argument::any())
            ->shouldBeCalled()
            ->willReturn($values);
        $cache->set(Argument::any())
            ->shouldNotBeCalled();

        $this->findWithResourceID('Game', 'resource_id')
            ->shouldBeLike($expected);
    }

    public function it_shortcuts_for_search_without_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = [
            ['name' => 'Value 1'],
            ['name' => 'Value 2'],
        ];
        $expected = [
            new Model('Game', $values[0]),
            new Model('Game', $values[1]),
        ];

        $apiCaller->fetch('https://www.example.com', 'api_key', Argument::type(CompiledQuery::class))
            ->shouldBeCalled()
            ->willReturn($values);

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);
        $cache->set(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $this->search('Value', 'Game')
            ->shouldBeLike($expected);
    }

    public function it_shortcuts_for_search_with_cache(CacheInterface $cache, ApiCaller $apiCaller): void
    {
        $values = [
            ['name' => 'Value 1'],
            ['name' => 'Value 2'],
        ];
        $expected = [
            new Model('Game', $values[0]),
            new Model('Game', $values[1]),
        ];

        $apiCaller->fetch(Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $cache->has(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);
        $cache->get(Argument::any())
            ->shouldBeCalled()
            ->willReturn($values);
        $cache->set(Argument::any())
            ->shouldNotBeCalled();

        $this->search('Value', 'Game')
            ->shouldBeLike($expected);
    }
}
