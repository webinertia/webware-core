<?php

declare(strict_types=1);

namespace Webware\Core;

use DateTimeImmutable;
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
// @mago-expect lint:too-many-methods,too-many-properties - accepted: the user row contract is a single aggregate — the builders and the row's columns belong on it together.
// @mago-expect lint:no-boolean-flag-parameter - accepted: withActive(bool) mirrors the entity's builder API and the with* convention, where a value change stays with*.
interface UserInterface extends
    MezzioUserInterface,
    RoleInterface,
    ResourceInterface,
    ProprietaryInterface,
    RowPrototypeInterface
{
    public const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** Primary key; null until persisted. */
    public int|string|null $id { get; }

    /** A role name, not a RoleInterface. */
    public string $roleId { get; }

    public ?string $firstName { get; }

    public ?string $lastName { get; }

    /** Lowercased by the implementation. */
    public ?string $email { get; }

    /** Server-side only. */
    public ?string $passwordHash { get; }

    /** Read side of withActive(). */
    public int|bool|null $active { get; }

    /** @var DateTimeImmutable|array<array-key, mixed>|string|null */
    public DateTimeImmutable|array|string|null $createdAt { get; }

    /** Server-side only. */
    public ?string $verificationToken { get; }

    /** @var DateTimeImmutable|array<array-key, mixed>|string|null */
    public DateTimeImmutable|array|string|null $tokenCreatedAt { get; }

    /**
     * Unmapped row columns; getDetail() reads only this.
     *
     * @var array<string, mixed>|null
     */
    public array|string|null $details { get; }

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
