<?php

declare(strict_types=1);

namespace Webware\Core;

use PhpDb\Sql\TableIdentifier;

/**
 * @api
 */
interface SchemaInterface
{
    public function table(): TableIdentifier;
}
