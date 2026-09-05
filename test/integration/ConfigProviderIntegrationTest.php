<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\ConfigProvider;
use Webware\Core\Container\SchemaFactoryFactory;
use Webware\Core\SchemaFactory;
use Webware\Core\SchemaInterface;

#[CoversClass(ConfigProvider::class)]
#[CoversMethod(ConfigProvider::class, '__invoke')]
final class ConfigProviderIntegrationTest extends TestCase
{
    #[Test]
    public function configProviderReturnsDependenciesAndSchemaConfig(): void
    {
        $config = (new ConfigProvider())();

        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey(SchemaInterface::class, $config);
        self::assertSame([], $config[SchemaInterface::class]);
        self::assertSame(
            SchemaFactoryFactory::class,
            $config['dependencies']['factories'][SchemaFactory::class],
        );
    }
}
