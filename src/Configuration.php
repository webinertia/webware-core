<?php

declare(strict_types=1);

namespace Webware\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function str_replace;

/**
 * @mago-expect analysis:class-must-be-final
 */
readonly class Configuration implements ConfigurationInterface
{
    private function __construct() {}

    /**
     * Prefix for this component's routes inside the admin namespace, e.g.
     * `admin.user.`.
     *
     * $adminName is the admin namespace base, resolved through webware-admin's
     * Configuration so every component that publishes admin routes agrees on it.
     */
    public static function getAdminRouteNamePrefix(string $adminName): string
    {
        return $adminName . '.' . static::COMPONENT_NAME . '.';
    }

    /**
     * URI segment this component's admin routes are mounted on, e.g.
     * `admin/user-manager`.
     */
    public static function getAdminRouteSegment(string $adminName): string
    {
        return $adminName . '/' . static::getRouteSegment();
    }

    /**
     * This component's config block, keyed by its own CONFIG_KEY.
     *
     * @throws ContainerExceptionInterface
     * @return array<string, mixed>
     */
    final public static function getConfig(ContainerInterface $container, string $callingFactory): array
    {
        if (! $container->has('config')) {
            throw Exception\ContainerException::forMissingConfigService('config', $callingFactory);
        }

        /** @var array<string, mixed> $config */
        $config = $container->get('config');

        /** @var array<string, mixed> */
        return $config[static::CONFIG_KEY] ?? [];
    }

    /**
     * Prefix shared by every route name this component owns, e.g. `user.`.
     */
    public static function getRouteNamePrefix(): string
    {
        return static::COMPONENT_NAME . '.';
    }

    /**
     * URI segment this component's routes are mounted on, e.g. `user-manager`.
     */
    public static function getRouteSegment(): string
    {
        return str_replace(
            search : '.',
            replace: '-',
            subject: static::COMPONENT_NAME,
        );
    }
}
