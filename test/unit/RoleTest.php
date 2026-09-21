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
use function json_encode;

#[CoversClass(Role::class)]
#[CoversMethod(Role::class, 'getRoleId')]
#[CoversMethod(Role::class, 'getRoles')]
#[CoversMethod(Role::class, 'jsonSerialize')]
final class RoleTest extends TestCase
{
    #[Test]
    public function everySeedRoleAndParentResolvesToACase(): void
    {
        foreach (Role::getRoles() as $seed) {
            self::assertNotNull(Role::tryFrom($seed['roleId']));
            foreach ($seed['parentIds'] as $parentId) {
                self::assertNotNull(Role::tryFrom($parentId));
            }
        }
    }

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
    public function getRolesReturnsTheDefaultHierarchyParentsFirst(): void
    {
        self::assertSame(
            [
                ['roleId' => 'Guest', 'parentIds' => []],
                ['roleId' => 'Member', 'parentIds' => ['Guest']],
                ['roleId' => 'Administrator', 'parentIds' => ['Member']],
                ['roleId' => 'Developer', 'parentIds' => ['Administrator']],
            ],
            Role::getRoles(),
        );
    }

    #[Test]
    public function jsonSerializeReturnsTheFullDefaultMapForEveryCase(): void
    {
        $expected = Role::getRoles();

        foreach (Role::cases() as $role) {
            self::assertSame($expected, $role->jsonSerialize());
        }

        self::assertSame(json_encode($expected), json_encode(Role::Guest));
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
