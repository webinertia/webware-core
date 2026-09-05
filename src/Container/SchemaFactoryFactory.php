<?php

declare(strict_types=1);

namespace Webware\Core\Container;

use Psl\Type;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Webware\Core\ConfigProvider;
use Webware\Core\SchemaFactory;
use Webware\Core\SchemaInterface;

/**
 * @import-type SchemaConfig from ConfigProvider
 */
final class SchemaFactoryFactory
{
    /**
     * @throws Type\Exception\AssertException If the configured schema config does not match the expected shape.
     * @throws NotFoundExceptionInterface If the config service cannot be resolved.
     * @throws ContainerExceptionInterface If retrieving the config service fails.
     */
    public function __invoke(ContainerInterface $container): SchemaFactory
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') ?? [] : [];

        /** @var SchemaConfig $schemaConfig */
        $schemaConfig = $config[SchemaInterface::class] ?? [];

        return new SchemaFactory($schemaConfig);
    }
}
