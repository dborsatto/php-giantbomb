<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace spec\DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\ConfigurationException;
use DBorsatto\GiantBomb\RepositoryInterface;
use PhpSpec\ObjectBehavior;

class ConfigurationSpec extends ObjectBehavior
{
    public function it_creates_with_defaults(): void
    {
        $this->beConstructedWith('api_key');

        $this->getApiKey()
            ->shouldBe('api_key');
        $this->shouldNotThrow(ConfigurationException::invalidRepositoryName('Game'))
            ->during('getRepository', ['Game']);
        $this->shouldThrow(ConfigurationException::invalidRepositoryName('Invalid'))
            ->during('getRepository', ['Invalid']);
    }

    public function it_creates_with_custom_repositories(RepositoryInterface $repository): void
    {
        $repository->getName()
            ->willReturn('Custom');

        $this->beConstructedWith('api_key', null, [$repository]);

        $this->shouldThrow(ConfigurationException::invalidRepositoryName('Game'))
            ->during('getRepository', ['Game']);
        $this->shouldNotThrow(ConfigurationException::invalidRepositoryName('Custom'))
            ->during('getRepository', ['Custom']);
    }
}
