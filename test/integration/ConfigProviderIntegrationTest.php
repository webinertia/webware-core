<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\ConfigProvider;
use Webware\Core\Container\AttachCoreServicesMiddlewareFactory;
use Webware\Core\Container\SchemaFactoryFactory;
use Webware\Core\Http\Middleware\AttachCoreServicesMiddleware;
use Webware\Core\SchemaFactory;
use Webware\Core\SchemaInterface;

#[CoversClass(ConfigProvider::class)]
#[CoversMethod(ConfigProvider::class, '__invoke')]
#[CoversMethod(ConfigProvider::class, 'getDependencies')]
#[CoversMethod(ConfigProvider::class, 'getSchemaConfig')]
final class ConfigProviderIntegrationTest extends TestCase
{
    #[Test]
    public function configProviderReturnsDependenciesAndSchemaConfig(): void
    {
        $provider = new ConfigProvider();

        $dependencies = $provider->getDependencies();
        self::assertSame(
            AttachCoreServicesMiddlewareFactory::class,
            $dependencies['factories'][AttachCoreServicesMiddleware::class],
        );
        self::assertSame(
            SchemaFactoryFactory::class,
            $dependencies['factories'][SchemaFactory::class],
        );

        self::assertSame([], $provider->getSchemaConfig());

        $config = $provider();

        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey(SchemaInterface::class, $config);
        self::assertSame([], $config[SchemaInterface::class]);
    }
}
