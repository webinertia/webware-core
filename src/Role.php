<?php

declare(strict_types=1);

namespace Webware\Core;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Override;

/**
 * Single source of truth for the default roles webware provides.
 *
 * A case may be passed straight to Laminas ACL — `isAllowed(Role::Administrator)`,
 * `hasRole()`, `addRole()`, `inheritsRole()` — because the role registry accepts a
 * RoleInterface and resolves it through getRoleId(). Use `->value` only where a
 * plain string is required: database values, session payloads, array keys, and
 * `string` typed properties.
 *
 * Role inheritance is not expressed here. Parent/child registration is ACL
 * configuration, not a property of a role name.
 *
 * @api
 */
enum Role: string implements RoleInterface
{
    case Guest         = 'Guest';
    case Member        = 'Member';
    case Administrator = 'Administrator';
    case Developer     = 'Developer';

    #[Override]
    public function getRoleId(): string
    {
        return $this->value;
    }
}
