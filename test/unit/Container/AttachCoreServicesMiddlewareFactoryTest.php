<?php

declare(strict_types=1);

namespace WebwareTest\Core\Container;

use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Webware\Core\Container\AttachCoreServicesMiddlewareFactory;
use Webware\Core\Http\Middleware\AttachCoreServicesMiddleware;

#[CoversClass(AttachCoreServicesMiddlewareFactory::class)]
#[CoversMethod(AttachCoreServicesMiddlewareFactory::class, '__invoke')]
final class AttachCoreServicesMiddlewareFactoryTest extends TestCase
{
    #[Test]
    public function itBuildsMiddlewareWithInputFilterPluginManager(): void
    {
        $pluginManager = new InputFilterPluginManager(new ServiceManager());

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(InputFilterPluginManager::class)
            ->willReturn($pluginManager);

        $middleware = (new AttachCoreServicesMiddlewareFactory())($container);

        self::assertInstanceOf(AttachCoreServicesMiddleware::class, $middleware);
    }
}
