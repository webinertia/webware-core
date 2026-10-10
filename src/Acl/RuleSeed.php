<?php

declare(strict_types=1);

namespace Webware\Core\Acl;

/**
 * One row destined for the rules table, as declared by a component that owns
 * routes.
 *
 * `$resourceId` is a node in the ACL resource tree:
 *
 * - a registered route name - the usual case, and the only valid case for a row
 *   that names a `$parentResourceId`;
 * - an anchor node such as `user` or `admin.acl`, which is the unit of grant: a
 *   role allowed the anchor inherits every node beneath it. The anchor of a
 *   route tree is the route-name prefix without its trailing dot.
 *
 * `$parentResourceId` is both authorization topology and presentation topology:
 * it decides where the row sits in the ACL resource tree, and which node expands
 * to reveal it in the ACL administration screens. A row with no parent becomes a
 * root resource, which detaches it from any anchor above it.
 *
 * @api
 */
final readonly class RuleSeed
{
    /**
     * @param list<string> $assertions assertion alias strings; empty means none
     */
    public function __construct(
        public RuleType $type,
        public string $roleId,
        public string $resourceId,
        public array $assertions = [],
        public ?string $parentResourceId = null,
    ) {}

    /**
     * Structural equality.
     *
     * Two providers describing the same (role, resource) pair differently is a
     * conflict worth reporting; two instances carrying identical values are not.
     * Identity comparison cannot express that, so the fields are compared here.
     */
    public function equals(RuleSeed $other): bool
    {
        return (
            $this->type === $other->type
                && $this->roleId === $other->roleId
                && $this->resourceId === $other->resourceId
                && $this->assertions === $other->assertions
                && $this->parentResourceId === $other->parentResourceId
        );
    }
}
