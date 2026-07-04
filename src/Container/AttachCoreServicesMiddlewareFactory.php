<?php

declare(strict_types=1);

namespace Webware\Core\Container;

use Laminas\InputFilter\InputFilterPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Webware\Core\Http\Middleware\AttachCoreServicesMiddleware;

final class AttachCoreServicesMiddlewareFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AttachCoreServicesMiddleware
    {
        return new AttachCoreServicesMiddleware(
            $container->get(InputFilterPluginManager::class),
        );
    }
}
