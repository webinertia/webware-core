<?php

declare(strict_types=1);

namespace WebwareTest\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Webware\Core\Configuration;
use Webware\Core\Exception\ContainerException;

#[CoversClass(Configuration::class)]
#[CoversMethod(Configuration::class, 'getConfig')]
#[CoversMethod(Configuration::class, 'getAdminRouteNamePrefix')]
#[CoversMethod(Configuration::class, 'getAdminRouteSegment')]
#[CoversMethod(Configuration::class, 'getRouteNamePrefix')]
#[CoversMethod(Configuration::class, 'getRouteSegment')]
final class ConfigurationTest extends TestCase
{
    private const CONFIG = [
        'webware' => [
            'admin_route_name_prefix' => 'webware.admin.',
            'admin_route_segment'     => 'webware.admin',
            'route_name_prefix'       => 'webware.',
            'route_segment'           => 'webware',
        ],
    ];

    #[Test]
    public function itReturnsAdminRouteNamePrefix(): void
    {
        $container = $this->container(self::CONFIG);

        self::assertSame('webware.admin.', Configuration::getAdminRouteNamePrefix($container, self::class));
    }

    #[Test]
    public function itReturnsAdminRouteSegment(): void
    {
        $container = $this->container(self::CONFIG);

        self::assertSame('webware.admin', Configuration::getAdminRouteSegment($container, self::class));
    }

    #[Test]
    public function itReturnsRouteNamePrefix(): void
    {
        $container = $this->container(self::CONFIG);

        self::assertSame('webware.', Configuration::getRouteNamePrefix($container, self::class));
    }

    #[Test]
    public function itReturnsRouteSegment(): void
    {
        $container = $this->container(self::CONFIG);

        self::assertSame('webware', Configuration::getRouteSegment($container, self::class));
    }

    #[Test]
    public function itReturnsWebwareConfig(): void
    {
        $container = $this->container(self::CONFIG);

        self::assertSame(self::CONFIG['webware'], Configuration::getConfig($container, self::class));
    }

    #[Test]
    public function itThrowsWhenConfigServiceMissing(): void
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(false);

        $this->expectException(ContainerException::class);

        Configuration::getConfig($container, self::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function container(array $config): ContainerInterface
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($config);

        return $container;
    }
}
