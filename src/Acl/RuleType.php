<?php

declare(strict_types=1);

namespace Webware\Core\Acl;

use Laminas\Permissions\Acl\Acl as LaminasAcl;

/**
 * The two rule types, as stored in the `type` column of the rules table.
 *
 * This lives in core rather than in the package that owns the ACL tables,
 * because rule seeds are contributed by every component that owns routes: the
 * enum is part of the seeding contract, not of the ACL implementation.
 *
 * @api
 */
enum RuleType: string
{
    case Allow = 'Allow';
    case Deny  = 'Deny';

    public function toAclConstant(): string
    {
        return match ($this) {
            self::Allow => LaminasAcl::TYPE_ALLOW,
            self::Deny => LaminasAcl::TYPE_DENY,
        };
    }
}
