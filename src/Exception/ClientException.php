<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Exception;

use Psr\SimpleCache\CacheException;
use function sprintf;

class ClientException extends SdkException
{
    public static function cacheError(CacheException $exception): self
    {
        return new self(sprintf(
            'An error working with the cache has occurred: "%s"',
            $exception->getMessage()
        ));
    }
}
