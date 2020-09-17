<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb;

use DBorsatto\GiantBomb\Exception\SdkException;

interface ClientInterface
{
    /**
     * @param string $repositoryName
     * @param Query  $query
     *
     * @throws SdkException
     *
     * @return Model[]
     */
    public function find(string $repositoryName, Query $query): array;

    /**
     * @param string $repositoryName
     * @param Query  $query
     *
     * @throws SdkException
     *
     * @return Model
     */
    public function findOne(string $repositoryName, Query $query): Model;
}
