<?php

declare(strict_types=1);

namespace WebwareTest\Core\Http\Middleware;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webware\Core\Http\Middleware\AttachCoreServicesMiddleware;

#[CoversClass(AttachCoreServicesMiddleware::class)]
#[CoversMethod(AttachCoreServicesMiddleware::class, '__construct')]
#[CoversMethod(AttachCoreServicesMiddleware::class, 'process')]
final class AttachCoreServicesMiddlewareTest extends TestCase
{
    #[Test]
    public function itAttachesInputFilterPluginManagerToRequest(): void
    {
        $pluginManager = new InputFilterPluginManager(new ServiceManager());
        $middleware    = new AttachCoreServicesMiddleware($pluginManager);

        $expectedResponse = new EmptyResponse();
        $capturedRequest  = null;

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturnCallback(
                static function (ServerRequestInterface $request) use (&$capturedRequest, $expectedResponse) {
                    $capturedRequest = $request;

                    return $expectedResponse;
                },
            );

        $response = $middleware->process(new ServerRequest(), $handler);

        self::assertSame($expectedResponse, $response);
        self::assertSame($pluginManager, $capturedRequest?->getAttribute(InputFilterPluginManager::class));
    }
}
