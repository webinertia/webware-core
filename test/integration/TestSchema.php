<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core;

use Webware\Core\SchemaInterface;

enum TestSchema: string implements SchemaInterface
{
    case Roles = 'core_role';
}
