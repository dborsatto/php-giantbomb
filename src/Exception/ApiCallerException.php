<?php

declare(strict_types=1);

/**
 * This file is part of the dborsatto/php-giantbomb package.
 *
 * @license MIT
 */

namespace DBorsatto\GiantBomb\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use function sprintf;

class ApiCallerException extends SdkException
{
    public static function httpClientError(ClientExceptionInterface $exception): self
    {
        return new self(sprintf(
            'An error working with the HTTP client has occurred: "%s"',
            $exception->getMessage()
        ));
    }

    public static function invalidAPIResponse(): self
    {
        return new self('Query to the API server did not result in an appropriate response');
    }
}
