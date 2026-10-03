<?php

declare(strict_types=1);

namespace Webware\Core\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function sprintf;

final class ContainerException extends RuntimeException implements ExceptionInterface, ContainerExceptionInterface
{
    public static function forMissingConfigService(string $serviceName, string $currentFactory): static
    {
        return new self(
            sprintf(
                'The "%s" service was not found in the container. Requested by factory: %s',
                $serviceName,
                $currentFactory,
            ),
        );
    }
}
