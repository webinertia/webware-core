<?php

declare(strict_types=1);

namespace WebwareTest\Core\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Webware\Core\Exception\ContainerException;
use Webware\Core\Exception\ExceptionInterface;

#[CoversClass(ContainerException::class)]
#[CoversMethod(ContainerException::class, 'forMissingConfigService')]
final class ContainerExceptionTest extends TestCase
{
    #[Test]
    public function forMissingConfigServiceBuildsMessage(): void
    {
        $exception = ContainerException::forMissingConfigService('config', 'Factory');

        self::assertSame(
            'The "config" service was not found in the container. Requested by factory: Factory',
            $exception->getMessage(),
        );
        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertInstanceOf(ContainerExceptionInterface::class, $exception);
    }
}
