<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Exception;

use function implode;
use function sprintf;

class ModelException extends SdkException
{
    public static function invalidMagicGetter(string $method, string $modelName): self
    {
        return new self(sprintf(
            'Invalid call to magic method "%s" on model "%s"',
            $method,
            $modelName
        ));
    }

    /**
     * @param string        $value
     * @param array<string> $available
     *
     * @return self
     */
    public static function invalidValue(string $value, array $available): self
    {
        return new self(sprintf(
            'Value "%s" is not a valid key, expecting one of "%s"',
            $value,
            implode(', ', $available)
        ));
    }
}
