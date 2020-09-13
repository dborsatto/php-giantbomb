<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Exception;

use function sprintf;

class ConfigurationException extends SdkException
{
    public static function invalidRepositoryName(string $name): self
    {
        return new self(sprintf('Repository "%s" is not supported', $name));
    }
}
