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
// @mago-expect lint:too-many-methods - accepted: the user row contract is a single aggregate; splitting it would scatter the builders their callers use together.
// @mago-expect lint:no-boolean-flag-parameter - accepted: withActive(bool) mirrors the entity's builder API and the with* convention, where a value change stays with*.
interface UserInterface extends
    MezzioUserInterface,
    RoleInterface,
    ResourceInterface,
    ProprietaryInterface,
    RowPrototypeInterface
{
    public const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Whether this user account is active.
     *
     * The declared type mirrors the row rather than the concept: a database column
     * delivers 1/0 as an int, a hydrated row delivers bool, and the value is null
     * until one is set. Reading applies the implementation's normalization, so an
     * unset value reads as false. This is the read side of withActive().
     *
     * A get hook only — the write side is withActive(), so an implementation keeps
     * its setter private.
     */
    public int|bool|null $active { get; }

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
     * Hydrate this user from a row of data.
     *
     * Inherited from RowPrototypeInterface; redeclared only to carry this docblock.
     *
     * @param array<array-key, mixed> $data
     */
    #[Override]
    public function populate(array $data): RowPrototypeInterface;

    /**
     * Return a copy of this user with the given active flag.
     */
    public function withActive(bool $active): static;

    /**
     * Return a copy of this user with the given detail set.
     */
    public function withDetail(string $name, mixed $value): static;

    /**
     * Return a copy of this user with the given email address.
     */
    public function withEmail(string $email): static;

    /**
     * Return a copy of this user with the given first name.
     */
    public function withFirstName(string $firstName): static;

    /**
     * Return a copy of this user with the given identity.
     *
     * The identity is whatever this implementation nominates — an email address,
     * a username, an id. It is the value getIdentity() returns, and it is not
     * assumed to be an email address.
     *
     * The row id is not settable here: it is assigned by the constructor and by
     * persisting the row, never by a builder.
     */
    public function withIdentity(string $identity): static;

    /**
     * Return a copy of this user with the given last name.
     */
    public function withLastName(string $lastName): static;

    /**
     * Return a copy of this user with the given password hash.
     */
    public function withPasswordHash(string $passwordHash): static;

    /**
     * Return a copy of this user with the given role id.
     *
     * A user carries exactly one role. Laminas ACL resolves that role through
     * getRoleId(), so the role id is a plain string here and the array Mezzio's
     * contract asks for exists only on the getRoles() return.
     *
     * @param string $roleId
     */
    public function withRoleId(string $roleId): static;
}
