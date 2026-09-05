<?php

declare(strict_types=1);

namespace WebwareTest\Core\InputFilter;

use Laminas\InputFilter\ErrorMessages;
use Override;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Webware\Core\InputFilter\SystemMessageTrait;

#[CoversTrait(SystemMessageTrait::class)]
final class SystemMessageTraitTest extends TestCase
{
    private object $trait;

    #[Test]
    public function flattenMessagesSkipsNonStringLeafValues(): void
    {
        $result = $this->flattenMessages(['roleId' => ['count' => 5, 'isEmpty' => 'Required']]);

        self::assertSame(['roleId: Required'], $result);
    }

    #[Test]
    public function getSystemMessageFlattensMessages(): void
    {
        $messages = new ErrorMessages([
            'roleId'   => 'Value is required',
            'parentId' => ['isEmpty' => 'Value is required and cannot be empty'],
            'fieldset' => ['nested' => ['isEmpty' => 'Nested message']],
        ]);

        $result = $this->trait->getSystemMessage($messages);

        self::assertSame(
            'roleId: Value is required<br />parentId: Value is required and cannot be empty<br />fieldset.nested: Nested message',
            $result,
        );
    }

    #[Test]
    public function getSystemMessageReturnsJson(): void
    {
        $messages = new ErrorMessages(['roleId' => 'Value is required']);

        $result = $this->trait->getSystemMessage($messages, true);

        self::assertSame('{"roleId":"Value is required"}', $result);
    }

    #[Test]
    public function isLeafMessageSetDetectsNestedArrays(): void
    {
        self::assertTrue($this->isLeafMessageSet(['isEmpty' => 'Required']));
        self::assertFalse($this->isLeafMessageSet(['nested' => ['isEmpty' => 'Required']]));
    }

    #[Override]
    protected function setUp(): void
    {
        $this->trait = new class() {
            use SystemMessageTrait;
        };
    }

    /**
     * @param array<array-key, string|array<array-key, string|array<array-key, string>>> $messages
     * @return string[]
     */
    private function flattenMessages(array $messages): array
    {
        return new ReflectionMethod($this->trait, 'flattenMessages')->invoke($this->trait, $messages);
    }

    private function isLeafMessageSet(array $message): bool
    {
        return new ReflectionMethod($this->trait, 'isLeafMessageSet')->invoke($this->trait, $message);
    }
}
