<?php

declare(strict_types=1);

namespace WebwareTest\Core\Acl;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\Acl\RuleSeed;
use Webware\Core\Acl\RuleType;

#[CoversClass(RuleSeed::class)]
#[CoversMethod(RuleSeed::class, '__construct')]
#[CoversMethod(RuleSeed::class, 'equals')]
final class RuleSeedTest extends TestCase
{
    #[Test]
    public function defaultsToNoAssertionsAndNoParent(): void
    {
        $ruleSeed = new RuleSeed(
            type      : RuleType::Allow,
            roleId    : 'Guest',
            resourceId: 'user.session.read',
        );

        self::assertSame([], $ruleSeed->assertions);
        self::assertNull($ruleSeed->parentResourceId);
    }

    #[Test]
    public function differsWhenTheAssertionsDiffer(): void
    {
        self::assertFalse($this->seed()->equals(other: $this->seed(assertions: ['Ownership'])));
    }

    #[Test]
    public function differsWhenTheParentDiffers(): void
    {
        self::assertFalse($this->seed()->equals(other: $this->seed(parentResourceId: 'admin')));
    }

    #[Test]
    public function differsWhenTheResourceDiffers(): void
    {
        self::assertFalse($this->seed()->equals(other: $this->seed(resourceId: 'user.register.read')));
    }

    #[Test]
    public function differsWhenTheRoleDiffers(): void
    {
        self::assertFalse($this->seed()->equals(other: $this->seed(roleId: 'Member')));
    }

    #[Test]
    public function differsWhenTheTypeDiffers(): void
    {
        self::assertFalse($this->seed()->equals(other: $this->seed(type: RuleType::Deny)));
    }

    #[Test]
    public function equalsItselfWhenEveryFieldMatches(): void
    {
        $ruleSeed = $this->seed();
        $same     = $this->seed();

        self::assertTrue($ruleSeed->equals(other: $same));
        self::assertTrue($ruleSeed->equals(other: $ruleSeed));
    }

    /**
     * @param list<string> $assertions
     */
    private function seed(
        RuleType $type = RuleType::Allow,
        string $roleId = 'Guest',
        string $resourceId = 'user.session.read',
        array $assertions = [],
        ?string $parentResourceId = 'user',
    ): RuleSeed {
        return new RuleSeed(
            type            : $type,
            roleId          : $roleId,
            resourceId      : $resourceId,
            assertions      : $assertions,
            parentResourceId: $parentResourceId,
        );
    }
}
