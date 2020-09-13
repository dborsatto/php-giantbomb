<?php

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\ConfigurationException;
use DBorsatto\GiantBomb\Repository\AccessoryRepository;
use DBorsatto\GiantBomb\Repository\CharacterRepository;
use DBorsatto\GiantBomb\Repository\ChatRepository;
use DBorsatto\GiantBomb\Repository\CompanyRepository;
use DBorsatto\GiantBomb\Repository\ConceptRepository;
use DBorsatto\GiantBomb\Repository\FranchiseRepository;
use DBorsatto\GiantBomb\Repository\GameRatingRepository;
use DBorsatto\GiantBomb\Repository\GameRepository;
use DBorsatto\GiantBomb\Repository\GenreRepository;
use DBorsatto\GiantBomb\Repository\LocationRepository;
use DBorsatto\GiantBomb\Repository\ObjectRepository;
use DBorsatto\GiantBomb\Repository\PersonRepository;
use DBorsatto\GiantBomb\Repository\PlatformRepository;
use DBorsatto\GiantBomb\Repository\PromoRepository;
use DBorsatto\GiantBomb\Repository\RatingBoardRepository;
use DBorsatto\GiantBomb\Repository\RegionRepository;
use DBorsatto\GiantBomb\Repository\ReleaseRepository;
use DBorsatto\GiantBomb\Repository\ReviewRepository;
use DBorsatto\GiantBomb\Repository\SearchRepository;
use DBorsatto\GiantBomb\Repository\ThemeRepository;
use DBorsatto\GiantBomb\Repository\TypeRepository;
use DBorsatto\GiantBomb\Repository\UserReviewRepository;
use DBorsatto\GiantBomb\Repository\VideoRepository;
use DBorsatto\GiantBomb\Repository\VideoTypeRepository;

class Configuration
{
    private const DEFAULT_API_ENDPOINT = 'http://www.giantbomb.com/api/';

    private string $apiKey;

    private string $apiEndpoint;

    /**
     * @var RepositoryInterface[]
     */
    private array $repositories;

    /**
     * @param string                     $apiKey
     * @param string|null                $apiEndpoint
     * @param RepositoryInterface[]|null $repositories
     */
    public function __construct(string $apiKey, ?string $apiEndpoint = null, array $repositories = null)
    {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint ?? self::DEFAULT_API_ENDPOINT;
        $this->repositories = $repositories ?? [
            new AccessoryRepository(),
            new CharacterRepository(),
            new ChatRepository(),
            new CompanyRepository(),
            new ConceptRepository(),
            new FranchiseRepository(),
            new GameRatingRepository(),
            new GameRepository(),
            new GenreRepository(),
            new LocationRepository(),
            new ObjectRepository(),
            new PersonRepository(),
            new PlatformRepository(),
            new PromoRepository(),
            new RatingBoardRepository(),
            new RegionRepository(),
            new ReleaseRepository(),
            new ReviewRepository(),
            new SearchRepository(),
            new ThemeRepository(),
            new TypeRepository(),
            new UserReviewRepository(),
            new VideoRepository(),
            new VideoTypeRepository(),
        ];
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * @param string $name
     *
     * @throws ConfigurationException
     *
     * @return RepositoryInterface
     */
    public function getRepository(string $name): RepositoryInterface
    {
        foreach ($this->repositories as $repository) {
            if ($repository->getName() === $name) {
                return $repository;
            }
        }

        throw ConfigurationException::invalidRepositoryName($name);
    }
}
