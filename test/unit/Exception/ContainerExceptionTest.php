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
#[CoversMethod(ContainerException::class, 'forEmptyConfiguration')]
#[CoversMethod(ContainerException::class, 'forInvalidConfigType')]
#[CoversMethod(ContainerException::class, 'forMissingConfigKey')]
#[CoversMethod(ContainerException::class, 'forMissingConfigService')]
final class ContainerExceptionTest extends TestCase
{
    #[Test]
    public function forEmptyConfigurationBuildsMessage(): void
    {
        $exception = ContainerException::forEmptyConfiguration('webware', 'Factory');

        self::assertSame(
            'Configuration key "webware" is present but empty in factory: Factory',
            $exception->getMessage(),
        );
        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertInstanceOf(ContainerExceptionInterface::class, $exception);
    }

    #[Test]
    public function forInvalidConfigTypeBuildsMessage(): void
    {
        $exception = ContainerException::forInvalidConfigType('webware', 'array', 'string', 'Factory');

        self::assertSame(
            'Configuration key "webware" is expected to be of type "array" in factory: Factory received type "string"',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function forMissingConfigKeyBuildsMessage(): void
    {
        $exception = ContainerException::forMissingConfigKey('webware', 'Factory');

        self::assertSame('Missing required config key: webware in factory: Factory', $exception->getMessage());
    }

    #[Test]
    public function forMissingConfigServiceBuildsMessage(): void
    {
        $exception = ContainerException::forMissingConfigService('config', 'Factory');

        self::assertSame(
            'The "config" service was not found in the container. Requested by factory: Factory',
            $exception->getMessage(),
        );
    }
}
