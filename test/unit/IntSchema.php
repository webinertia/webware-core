<?php

declare(strict_types=1);

namespace WebwareTest\Core;

use Webware\Core\SchemaInterface;

enum IntSchema: int implements SchemaInterface
{
    case Role = 1;
}
