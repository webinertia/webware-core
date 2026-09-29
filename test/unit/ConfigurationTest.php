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
use WebwareTest\Core\Stub\DottedComponentConfiguration;

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
            'some_key' => 'some value',
        ],
    ];

    #[Test]
    public function itDashesTheComponentNameForTheRouteSegment(): void
    {
        self::assertSame('webware', Configuration::getRouteSegment());
        self::assertSame('sales-order', DottedComponentConfiguration::getRouteSegment());
    }

    #[Test]
    public function itDerivesTheRouteNamePrefixFromTheComponentName(): void
    {
        self::assertSame('webware.', Configuration::getRouteNamePrefix());
        self::assertSame('sales.order.', DottedComponentConfiguration::getRouteNamePrefix());
    }

    #[Test]
    public function itNestsTheComponentUnderTheAdminNamespace(): void
    {
        self::assertSame('admin.sales.order.', DottedComponentConfiguration::getAdminRouteNamePrefix('admin'));
        self::assertSame('admin/sales-order', DottedComponentConfiguration::getAdminRouteSegment('admin'));
    }

    #[Test]
    public function itReturnsAnEmptyArrayWhenTheComponentBlockIsMissing(): void
    {
        $container = $this->container([]);

        self::assertSame([], Configuration::getConfig($container, self::class));
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

    #[Test]
    public function itUsesTheResolvedAdminNameForTheAdminNamespace(): void
    {
        self::assertSame(
            'control-panel.sales.order.',
            DottedComponentConfiguration::getAdminRouteNamePrefix('control-panel'),
        );
        self::assertSame(
            'control-panel/sales-order',
            DottedComponentConfiguration::getAdminRouteSegment('control-panel'),
        );
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
