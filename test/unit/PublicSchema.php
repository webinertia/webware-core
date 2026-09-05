<?php

declare(strict_types=1);

namespace WebwareTest\Core;

use Webware\Core\SchemaInterface;

enum PublicSchema: string implements SchemaInterface
{
    case Role = 'acl_role';

    public const string SCHEMA = 'public';
}
