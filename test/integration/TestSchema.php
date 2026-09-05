<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core;

use PhpDb\Sql\TableIdentifier;
use Webware\Core\SchemaInterface;

enum TestSchema: string implements SchemaInterface
{
    case Roles = 'core_role';

    public function table(): TableIdentifier
    {
        return new TableIdentifier(table: $this->value);
    }
}
