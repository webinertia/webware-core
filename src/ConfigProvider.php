<?php

declare(strict_types=1);

/**
 * This file is part of the Webware\Core package.
 *
 * Copyright (c) 2026 Joey Smith <jsmith@webinertia.net>
 * and contributors.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webware\Core;

final class ConfigProvider
{
    /**
     * @mago-return array{
     *      aliases: array<string, class-string>,
     *      invokables: array<class-string, class-string>,
     *      factories: array<class-string, class-string>
     * }
     */
    public function getDependencies(): array
    {
        return [
            'aliases'    => [],
            'invokables' => [],
            'factories'  => [
                Http\Middleware\AttachCoreServicesMiddleware::class =>
                    Container\AttachCoreServicesMiddlewareFactory::class,
            ],
        ];
    }

    /**
     * @mago-return array{
     *      dependencies: array{
     *          aliases: array<string, class-string>,
     *          invokables: array<class-string, class-string>,
     *          factories: array<class-string, class-string>
     *      }
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }
}
