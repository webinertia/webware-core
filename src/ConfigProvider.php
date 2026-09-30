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

use Mezzio\Authentication\UserInterface as MezzioUserInterface;

/**
 * @type Dependencies = array{
 *      aliases: array<interface-string, interface-string>,
 *      factories: array<class-string, class-string>
 * }
 * @type ProviderConfig = array{
 *      dependencies: Dependencies,
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
            // Ecosystem-wide alias: Webware\Core\UserInterface extends the Mezzio
            // authentication contract, so anything resolving that contract — a host,
            // or a Mezzio component — is handed our implementation instead of
            // Mezzio's DefaultUser. webware-usermanager registers the factory under
            // our own interface key.
            'aliases'   => [
                MezzioUserInterface::class => UserInterface::class,
            ],
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
