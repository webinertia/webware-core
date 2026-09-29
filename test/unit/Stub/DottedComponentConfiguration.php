<?php

declare(strict_types=1);

namespace WebwareTest\Core\Stub;

use Webware\Core\Configuration;

/**
 * Proves the derivations bind to the subclass constant, not to core's default.
 */
final readonly class DottedComponentConfiguration extends Configuration
{
    public const string COMPONENT_NAME = 'sales.order';
}
