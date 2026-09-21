<?php

declare(strict_types=1);

namespace Webware\Core;

use JsonSerializable;
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
 * The default hierarchy for seeding is returned by getRoles(); acl_role is authoritative at runtime.
 *
 * @api
 */
enum Role: string implements RoleInterface, JsonSerializable
{
    case Guest         = 'Guest';
    case Member        = 'Member';
    case Administrator = 'Administrator';
    case Developer     = 'Developer';

    /**
     * The default role set for seeding, parents before children.
     *
     * @return list<array{roleId: string, parentIds: list<string>}>
     */
    public static function getRoles(): array
    {
        return [
            ['roleId' => Role::Guest->value, 'parentIds' => []],
            ['roleId' => Role::Member->value, 'parentIds' => [Role::Guest->value]],
            ['roleId' => Role::Administrator->value, 'parentIds' => [Role::Member->value]],
            ['roleId' => Role::Developer->value, 'parentIds' => [Role::Administrator->value]],
        ];
    }

    #[Override]
    public function getRoleId(): string
    {
        return $this->value;
    }

    /**
     * Returns the full default role map; identical for every case.
     *
     * @return list<array{roleId: string, parentIds: list<string>}>
     */
    #[Override]
    public function jsonSerialize(): mixed
    {
        return self::getRoles();
    }
}
