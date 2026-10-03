<?php

declare(strict_types=1);

namespace WebwareTest\Core\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Webware\Core\Exception\ConfigurationException;
use Webware\Core\Exception\ExceptionInterface;

#[CoversClass(ConfigurationException::class)]
#[CoversMethod(ConfigurationException::class, 'forEmptyConfiguration')]
#[CoversMethod(ConfigurationException::class, 'forInvalidConfigType')]
#[CoversMethod(ConfigurationException::class, 'forMissingConfigKey')]
final class ConfigurationExceptionTest extends TestCase
{
    #[Test]
    public function forEmptyConfigurationBuildsMessage(): void
    {
        $exception = ConfigurationException::forEmptyConfiguration('webware', 'Factory');

        self::assertSame(
            'Configuration key "webware" is present but empty in factory: Factory',
            $exception->getMessage(),
        );
        self::assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[Test]
    public function forInvalidConfigTypeBuildsMessage(): void
    {
        $exception = ConfigurationException::forInvalidConfigType('webware', 'array', 'string', 'Factory');

        self::assertSame(
            'Configuration key "webware" is expected to be of type "array" in factory: Factory received type "string"',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function forMissingConfigKeyBuildsMessage(): void
    {
        $exception = ConfigurationException::forMissingConfigKey('webware', 'Factory');

        self::assertSame('Missing required config key: webware in factory: Factory', $exception->getMessage());
    }

    #[Test]
    public function isNotAPsrContainerException(): void
    {
        self::assertNotInstanceOf(
            ContainerExceptionInterface::class,
            ConfigurationException::forMissingConfigKey('webware', 'Factory'),
        );
    }
}
