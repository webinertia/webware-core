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

use PhpDb\Sql\TableIdentifier;

/**
 * @type Dependencies = array{
 *      factories: array<class-string, class-string>
 * }
 * @type SchemaConfig = array{
 *      prefix?: non-empty-string,
 *      separator?: non-empty-string,
 *      schema?: non-empty-string,
 *      prefixes?: array<string, non-empty-string>,
 *      schemas?: array<string, non-empty-string>,
 *      backup_prefix?: non-empty-string,
 *      backup_schema?: non-empty-string,
 * }
 * @type ProviderConfig = array{
 *      dependencies: Dependencies,
 *      Webware\Core\SchemaInterface: SchemaConfig,
 * }
 */
final class ConfigProvider
{
    public const string PREFIX_KEY        = 'prefix';
    public const string SEPARATOR_KEY     = 'separator';
    public const string SCHEMA_KEY        = 'schema';
    public const string PREFIXES_KEY      = 'prefixes';
    public const string SCHEMAS_KEY       = 'schemas';
    public const string BACKUP_PREFIX_KEY = 'backup_prefix';
    public const string BACKUP_SCHEMA_KEY = 'backup_schema';

    /**
     * @return Dependencies
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                Http\Middleware\AttachCoreServicesMiddleware::class => Container\AttachCoreServicesMiddlewareFactory::class,
                SchemaFactory::class                                => Container\SchemaFactoryFactory::class,
            ],
        ];
    }

    /**
     * @return SchemaConfig
     */
    public function getSchemaConfig(): array
    {
        return [
            self::SEPARATOR_KEY => TableIdentifier::SEPARATOR,
        ];
    }

    /**
     * @return ProviderConfig
     */
    public function __invoke(): array
    {
        return [
            'dependencies'         => $this->getDependencies(),
            SchemaInterface::class => $this->getSchemaConfig(),
        ];
    }
}
