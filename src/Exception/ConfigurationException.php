<?php

declare(strict_types=1);

namespace Webware\Core\Exception;

use RuntimeException;

use function sprintf;

/**
 * A configuration value the application code asked for is missing, empty or of
 * the wrong type.
 *
 * Deliberately not a PSR-11 `ContainerExceptionInterface`: this is thrown where
 * a factory - or any other caller - reads the `config` service, not because the
 * container itself failed.
 *
 * @api
 */
final class ConfigurationException extends RuntimeException implements ExceptionInterface
{
    public static function forEmptyConfiguration(string $key, string $currentFactory): self
    {
        return new self(
            sprintf(
                'Configuration key "%s" is present but empty in factory: %s',
                $key,
                $currentFactory,
            ),
        );
    }

    public static function forInvalidConfigType(
        string $key,
        string $expectedType,
        string $receivedType,
        string $currentFactory,
    ): self {
        return new self(
            sprintf(
                'Configuration key "%s" is expected to be of type "%s" in factory: %s received type "%s"',
                $key,
                $expectedType,
                $currentFactory,
                $receivedType,
            ),
        );
    }

    public static function forMissingConfigKey(string $key, string $currentFactory): self
    {
        return new self(
            sprintf(
                'Missing required config key: %s in factory: %s',
                $key,
                $currentFactory,
            ),
        );
    }
}
