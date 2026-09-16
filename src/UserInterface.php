<?php

declare(strict_types=1);

namespace Webware\Core;

use Laminas\Permissions\Acl\ProprietaryInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Mezzio\Authentication\UserInterface as MezzioUserInterface;
use Override;
use PhpDb\ResultSet\RowPrototypeInterface;

/**
 * Extends the Mezzio authentication contract, so a host may alias
 * Mezzio\Authentication\UserInterface to this interface and any type hint on
 * Mezzio's interface stays valid while migrating to webware-usermanager.
 *
 * The four inherited methods are redeclared only to carry docblocks; their
 * signatures must stay variance-compatible with Mezzio's.
 *
 * @api
 */
interface UserInterface extends
    MezzioUserInterface,
    RoleInterface,
    ResourceInterface,
    ProprietaryInterface,
    RowPrototypeInterface
{
    final public const string GUEST_ROLE = 'Guest';
    public const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Get a detail $name if present, $default otherwise.
     */
    #[Override]
    public function getDetail(string $name, mixed $default = null): mixed;

    /**
     * Get all the details.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function getDetails(): array;

    /**
     * Get the unique user identity (id, username, email address …)
     */
    #[Override]
    public function getIdentity(): string;

    /**
     * Get all user roles.
     *
     * Role names, not RoleInterface instances — Laminas ACL resolves the role
     * via getRoleId(), so the aggregate object is what carries ownership.
     *
     * @return iterable<int|string, string>
     */
    #[Override]
    public function getRoles(): iterable;

    /**
     * Create a new instance of this user with the given id.
     * Allows for a user to be created without an id,
     * and then have the id set after persisting to the database.
     *
     * @return static
     */
    public function withId(int|string|null $id): static;
}
