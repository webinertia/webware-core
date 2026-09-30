<?php

declare(strict_types=1);

namespace WebwareTest\Core\Acl;

use Laminas\Permissions\Acl\Acl as LaminasAcl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\Acl\RuleType;

#[CoversClass(RuleType::class)]
#[CoversMethod(RuleType::class, 'toAclConstant')]
final class RuleTypeTest extends TestCase
{
    #[Test]
    public function mapsOntoLaminasConstants(): void
    {
        self::assertSame(LaminasAcl::TYPE_ALLOW, RuleType::Allow->toAclConstant());
        self::assertSame(LaminasAcl::TYPE_DENY, RuleType::Deny->toAclConstant());
    }

    #[Test]
    public function storesTheDatabaseValues(): void
    {
        self::assertSame('Allow', RuleType::Allow->value);
        self::assertSame('Deny', RuleType::Deny->value);
    }
}
