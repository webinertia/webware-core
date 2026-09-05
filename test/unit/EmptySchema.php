<?php

declare(strict_types=1);

namespace WebwareTest\Core;

use Webware\Core\SchemaInterface;

enum EmptySchema: string implements SchemaInterface
{
    case Role = '';
}
