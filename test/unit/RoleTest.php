<?php

declare(strict_types=1);

namespace WebwareTest\Core;

use Laminas\Permissions\Acl\Role\RoleInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\Role;

use function array_map;

#[CoversClass(Role::class)]
#[CoversMethod(Role::class, 'getRoleId')]
final class RoleTest extends TestCase
{
    #[Test]
    public function exposesTheDefaultRoleNames(): void
    {
        self::assertSame(
            ['Guest', 'Member', 'Administrator', 'Developer'],
            array_map(static fn(Role $role): string => $role->value, Role::cases()),
        );
    }

    #[Test]
    public function getRoleIdReturnsTheBackedValue(): void
    {
        foreach (Role::cases() as $role) {
            self::assertSame($role->value, $role->getRoleId());
        }
    }

    #[Test]
    public function resolvesARoleFromAStoredString(): void
    {
        self::assertSame(Role::Developer, Role::from('Developer'));
        self::assertNull(Role::tryFrom('NotARole'));
    }

    #[Test]
    public function satisfiesTheLaminasRoleContract(): void
    {
        self::assertInstanceOf(RoleInterface::class, Role::Member);
    }
}
