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

/**
 * @type Dependencies = array{
 *      factories: array<class-string, class-string>
 * }
 * @type ProviderConfig = array{
 *      dependencies: Dependencies
 * }
 */
final class ConfigProvider
{
    /**
     * @return Dependencies
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                Http\Middleware\AttachCoreServicesMiddleware::class =>
                    Container\AttachCoreServicesMiddlewareFactory::class,
            ],
        ];
    }

    /**
     * @return ProviderConfig
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }
}
