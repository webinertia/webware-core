<?php

declare(strict_types=1);

namespace Webware\Core;

use Psr\Container\ContainerInterface;
use Webware\Core\Exception\ContainerException;

readonly class Configuration implements ConfigurationInterface
{
    private function __construct() {}

    public static function getAdminRouteNamePrefix(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ADMIN_ROUTE_NAME_PREFIX_KEY];
    }

    public static function getAdminRouteSegment(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ADMIN_ROUTE_SEGMENT_KEY];
    }

    /**
     *
     * @mago-return array{
     *   admin_route_name_prefix: string,
     *   admin_route_segment: string,
     *   route_name_prefix: string,
     *   route_segment: string,
     * }
     * @throws ContainerException
     */
    final public static function getConfig(ContainerInterface $container, string $callingFactory): array
    {
        if (! $container->has('config')) {
            throw Exception\ContainerException::forMissingConfigService('config', $callingFactory);
        }

        /** @mago-var array{'webware': array{admin_route_name_prefix: string, admin_route_segment: string, route_name_prefix: string, route_segment: string}} */
        $config = $container->get('config');

        return $config[static::CONFIG_KEY];
    }

    public static function getRouteNamePrefix(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ROUTE_NAME_PREFIX_KEY];
    }

    public static function getRouteSegment(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ROUTE_SEGMENT_KEY];
    }
}
