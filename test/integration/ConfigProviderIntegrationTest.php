<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core;

use Mezzio\Authentication\UserInterface as MezzioUserInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\ConfigProvider;
use Webware\Core\Container\AttachCoreServicesMiddlewareFactory;
use Webware\Core\Http\Middleware\AttachCoreServicesMiddleware;
use Webware\Core\UserInterface;

#[CoversClass(ConfigProvider::class)]
#[CoversMethod(ConfigProvider::class, '__invoke')]
#[CoversMethod(ConfigProvider::class, 'getDependencies')]
final class ConfigProviderIntegrationTest extends TestCase
{
    #[Test]
    public function configProviderReturnsDependencies(): void
    {
        $provider = new ConfigProvider();

        $dependencies = $provider->getDependencies();
        self::assertSame(
            AttachCoreServicesMiddlewareFactory::class,
            $dependencies['factories'][AttachCoreServicesMiddleware::class],
        );

        $config = $provider();

        self::assertArrayHasKey('dependencies', $config);
        self::assertSame($dependencies, $config['dependencies']);
    }

    #[Test]
    public function mezzioAuthenticationContractIsAliasedToOurUserInterface(): void
    {
        $dependencies = new ConfigProvider()->getDependencies();

        self::assertSame(
            UserInterface::class,
            $dependencies['aliases'][MezzioUserInterface::class],
        );
    }
}
