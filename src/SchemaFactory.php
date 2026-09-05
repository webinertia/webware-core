<?php

declare(strict_types=1);

namespace Webware\Core;

use PhpDb\Sql\Exception\InvalidArgumentException;
use PhpDb\Sql\TableIdentifier;
use Psl\Type;

/**
 * Callable factory producing {@see TableIdentifier} instances from a
 * {@see SchemaInterface}, applying an application-configured table prefix and
 * optional schema so raw string table/schema identifiers never leak into the
 * application layer.
 *
 * The factory is schema-driven: the {@see SchemaInterface} is the single
 * source of truth for the default identifier (table name, schema, separator
 * and its own optional prefix). Configuration — supplied under the
 * `SchemaInterface::class` top-level config key — layers app-wide defaults and
 * per-table overrides on top of it.
 *
 * Live identifier precedence, highest first:
 *
 * - prefix    : call-time `$prefix` > config `prefixes[$table]` > config `prefix` > SchemaInterface
 * - schema    : call-time `$schemaName` > config `schemas[$table]` > config `schema` > SchemaInterface
 * - separator : call-time `$separator` > config `separator` > SchemaInterface > `_`
 *
 * {@see self::backup()} applies the dedicated `backup_prefix` / `backup_schema`
 * configuration instead, e.g. persisting a `bck_*` copy of a table in a
 * dedicated `backup` schema before mutating the live table. The backup prefix
 * is independent of the live prefix.
 *
 * @import-type SchemaConfig from ConfigProvider
 *
 * Resolution applies a four-level precedence (call-time, per-table, app-wide,
 * SchemaInterface) to prefix, schema and separator independently, so the class
 * branches heavily by design.
 *
 * @mago-expect lint:cyclomatic-complexity
 *
 * @api
 */
final readonly class SchemaFactory
{
    /**
     * @var SchemaConfig
     */
    private array $config;

    /**
     * @param SchemaConfig $config
     *
     * @throws Type\Exception\AssertException If $config does not match the expected shape.
     */
    public function __construct(array $config = [])
    {
        $this->config = self::schemaConfigType()->assert($config);
    }

    /**
     * @return Type\TypeInterface<SchemaConfig>
     */
    private static function schemaConfigType(): Type\TypeInterface
    {
        return Type\shape([
            'prefix'        => Type\optional(Type\non_empty_string()),
            'separator'     => Type\optional(Type\non_empty_string()),
            'schema'        => Type\optional(Type\non_empty_string()),
            'prefixes'      => Type\optional(Type\dict(Type\non_empty_string(), Type\non_empty_string())),
            'schemas'       => Type\optional(Type\dict(Type\non_empty_string(), Type\non_empty_string())),
            'backup_prefix' => Type\optional(Type\non_empty_string()),
            'backup_schema' => Type\optional(Type\non_empty_string()),
        ]);
    }

    /**
     * Produces the backup-target identifier for the given schema, applying the
     * dedicated `backup_prefix` / `backup_schema` configuration.
     *
     * The backup prefix is independent of the live prefix: a configured
     * `backup_prefix` replaces the live prefix entirely, so backing up
     * `acl_role` with `backup_prefix: 'bck'` yields `bck_acl_role`.
     *
     * @throws InvalidArgumentException If an override or configured value is an empty string.
     */
    public function backup(
        SchemaInterface $schema,
        ?string $schemaName = null,
        ?string $prefix = null,
        ?string $separator = null,
    ): TableIdentifier {
        $base    = $schema->table();
        $name    = $base->getUnprefixedTable();
        $schemas = $this->config['schemas'] ?? [];

        return new TableIdentifier(
            table    : $name,
            schema   : $schemaName ?? $schemas[$name] ?? $this->config['backup_schema'] ?? $this->config['schema'] ?? $base->getSchema(),
            prefix   : $prefix ?? $this->config['backup_prefix'] ?? $base->getPrefix(),
            separator: $separator ?? $this->config['separator'] ?? $base->getSeparator(),
        );
    }

    public function getBackupPrefix(): ?string
    {
        return $this->config['backup_prefix'] ?? null;
    }

    public function getBackupSchema(): ?string
    {
        return $this->config['backup_schema'] ?? null;
    }

    public function getPrefix(): ?string
    {
        return $this->config['prefix'] ?? null;
    }

    public function getSchema(): ?string
    {
        return $this->config['schema'] ?? null;
    }

    public function getSeparator(): string
    {
        return $this->config['separator'] ?? TableIdentifier::SEPARATOR;
    }

    /**
     * @throws InvalidArgumentException If an override or configured value is an empty string.
     */
    public function __invoke(
        SchemaInterface $schema,
        ?string $schemaName = null,
        ?string $prefix = null,
        ?string $separator = null,
    ): TableIdentifier {
        $base     = $schema->table();
        $name     = $base->getUnprefixedTable();
        $prefixes = $this->config['prefixes'] ?? [];
        $schemas  = $this->config['schemas'] ?? [];

        return new TableIdentifier(
            table    : $name,
            schema   : $schemaName ?? $schemas[$name] ?? $this->config['schema'] ?? $base->getSchema(),
            prefix   : $prefix ?? $prefixes[$name] ?? $this->config['prefix'] ?? $base->getPrefix(),
            separator: $separator ?? $this->config['separator'] ?? $base->getSeparator(),
        );
    }
}
