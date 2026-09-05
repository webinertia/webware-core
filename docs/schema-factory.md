# Schema Factory — Config-Driven Table Identifiers

_Authored: 2026-09-05_

---

## Problem

Database table and schema names leak into the application layer as raw strings
— a schema enum carried only an *unprefixed* table name, and there was no way
for an application to apply an app-wide table prefix (or redirect a backup into
a different schema) without hardcoding strings at every call site.

This document defines `Webware\Core\SchemaFactory`, the single place where
application configuration meets the `Webware\Core\SchemaInterface` contract.

---

## Components

### `SchemaInterface`

A marker contract for string-backed enums. Each case value is an unprefixed
table name; the optional `SCHEMA` constant declares the schema identifier shared
by every table in the enum:

```php
enum Schema: string implements SchemaInterface
{
    case Rules = 'acl_rule';

    public const string SCHEMA = 'public';
}
```

The interface is deliberately config-free: it knows the table's *identity*
(its name and, via `SCHEMA`, its schema), not the environment's prefix policy.
Enums with no explicit schema omit the constant, inheriting the empty default
(the connection's default schema).

### `SchemaFactory`

A callable (`__invoke`) that consumes any `SchemaInterface` and returns a
configured `PhpDb\Sql\TableIdentifier`. It is built by `SchemaFactoryFactory`
from the `config` service.

```php
final readonly class SchemaFactory
{
    public function __invoke(
        BackedEnum&SchemaInterface $schema,
        ?string $schemaName = null,
        ?string $prefix = null,
        ?string $separator = null,
    ): TableIdentifier;

    public function backup(
        BackedEnum&SchemaInterface $schema,
        ?string $schemaName = null,
        ?string $prefix = null,
        ?string $separator = null,
    ): TableIdentifier;
}
```

`__invoke()` produces the **live** identifier; `backup()` produces the
**backup** identifier using the dedicated `backup_prefix` / `backup_schema`
configuration.

### `SchemaFactoryFactory`

The DI factory that reads `config[SchemaInterface::class]`, validates the shape
with `Psl\Type\shape()`, and returns a configured `SchemaFactory`. When no
config is present the factory is created with all defaults (no prefix, `_`
separator, no schema).

### `ConfigProvider`

Registers the wiring and documents the config shape:

```php
'factories' => [
    SchemaFactory::class => Container\SchemaFactoryFactory::class,
],
SchemaInterface::class => $this->getSchemaConfig(),
```

---

## Configuration

Configuration lives under the top-level `SchemaInterface::class` key, mirroring
how `MessageBusInterface::class` carries message-bus config:

```php
return [
    Webware\Core\SchemaInterface::class => [
        'prefix'        => 'ww',              // app-wide live prefix
        'backup_prefix' => 'bck',             // app-wide backup prefix
        'separator'     => '_',               // default '_'
        'schema'        => 'public',          // app-wide live schema
        'backup_schema' => 'backup',          // app-wide backup schema
        'prefixes'      => [                  // per-table live prefix, keyed by unprefixed name
            'acl_role' => 'acl',
        ],
        'schemas'       => [                  // per-table live schema, keyed by unprefixed name
            'acl_role' => 'tenant',
        ],
    ],
];
```

| Key | Type | Default | Applies to |
|-----|------|---------|-----------|
| `prefix` | `non-empty-string` or null | `null` | live |
| `backup_prefix` | `non-empty-string` or null | `null` | backup |
| `separator` | `non-empty-string` | `_` | both |
| `schema` | `non-empty-string` or null | `null` | live (and backup fallback) |
| `backup_schema` | `non-empty-string` or null | `null` | backup |
| `prefixes` | `array<non-empty-string, non-empty-string>` | `[]` | live |
| `schemas` | `array<non-empty-string, non-empty-string>` | `[]` | live |

Empty-string values are rejected by the shape assertion — they are never a
valid prefix, schema or separator. Unknown keys are also rejected, so a typo in
config fails fast.

The shape is published as a mago `@type` alias so consumers can import it:

```php
/**
 * @import-type SchemaConfig from Webware\Core\ConfigProvider
 *
 * @param SchemaConfig $schemaConfig
 */
```

---

## Precedence

Resolution picks the first non-null value. Highest first.

### Live (`__invoke`)

| Field | 1 | 2 | 3 | 4 |
|-------|---|---|---|---|
| prefix | call-time `$prefix` | `prefixes[table]` | `prefix` | — |
| schema | call-time `$schemaName` | `schemas[table]` | `schema` | `SCHEMA` const |
| separator | call-time `$separator` | `separator` | `_` | — |

### Backup (`backup`)

| Field | 1 | 2 | 3 | 4 | 5 |
|-------|---|---|---|---|---|
| prefix | call-time `$prefix` | `backup_prefix` | — | — | — |
| schema | call-time `$schemaName` | `schemas[table]` | `backup_schema` | `schema` | `SCHEMA` const |
| separator | call-time `$separator` | `separator` | `_` | — | — |

The backup prefix is **independent** of the live prefix: with `backup_prefix:
'bck'`, backing up `acl_role` yields `bck_acl_role`, never `bck_ww_acl_role`.

---

## Usage

### Live identifier

```php
final readonly class FetchAllRulesHandlerFactory
{
    public function __invoke(ContainerInterface $container): FetchAllRulesHandler
    {
        $schemaFactory = $container->get(SchemaFactory::class);

        return new FetchAllRulesHandler(
            new TableGateway(
                table: $schemaFactory(Schema::Rules),
                // ...
            ),
        );
    }
}
```

### Backup to the same schema

```php
$backup = $schemaFactory->backup(Schema::Rules);
// bck_acl_role
```

### Backup to a different schema

`backup_schema` is configured, so no call-site string is needed:

```php
$backup = $schemaFactory->backup(Schema::Rules);
// backup.bck_acl_role
```

Or redirected at call time:

```php
$backup = $schemaFactory->backup(Schema::Rules, schemaName: 'archive');
// archive.bck_acl_role
```

---

## Relationship to phpdb

`SchemaFactory` returns phpdb's `PhpDb\Sql\TableIdentifier`, resolving the
schema from the enum's `SCHEMA` constant (or configuration) and layering the
configured prefix.
The `Schema` naming (rather than `Table`) is deliberate: a table identifier is
one member of a schema, and "schema" is the addressable namespace across every
RDBMS phpdb targets — MySQL/MariaDB (`database` synonym), PostgreSQL, SQLite
(attached-DB qualifier), and future Oracle/SQL Server support. This contract may
migrate into the phpdb org so it is available to all phpdb consumers.
