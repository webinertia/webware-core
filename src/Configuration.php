<?php

declare(strict_types=1);

namespace Webware\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * @mago-expect analysis:class-must-be-final
 */
readonly class Configuration implements ConfigurationInterface
{
    private function __construct() {}

    /**
     * @throws ContainerExceptionInterface
     */
    public static function getAdminRouteNamePrefix(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ADMIN_ROUTE_NAME_PREFIX_KEY];
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public static function getAdminRouteSegment(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ADMIN_ROUTE_SEGMENT_KEY];
    }

    /**
     * @throws ContainerExceptionInterface
     * @return array{
     *   admin_route_name_prefix: string,
     *   admin_route_segment: string,
     *   route_name_prefix: string,
     *   route_segment: string,
     * }
     */
    final public static function getConfig(ContainerInterface $container, string $callingFactory): array
    {
        if (! $container->has('config')) {
            throw Exception\ContainerException::forMissingConfigService('config', $callingFactory);
        }

        /** @var array{'webware': array{admin_route_name_prefix: string, admin_route_segment: string, route_name_prefix: string, route_segment: string}} */
        $config = $container->get('config');

        return $config[static::CONFIG_KEY];
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public static function getRouteNamePrefix(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ROUTE_NAME_PREFIX_KEY];
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public static function getRouteSegment(ContainerInterface $container, string $callingFactory): string
    {
        $config = static::getConfig($container, $callingFactory);

        return $config[static::ROUTE_SEGMENT_KEY];
    }
}
