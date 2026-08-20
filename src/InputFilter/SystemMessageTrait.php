<?php

declare(strict_types=1);

namespace Webware\Core\InputFilter;

use function implode;
use function is_array;
use function is_string;
use function json_encode;

/**
 * @api
 * @mixin \Laminas\InputFilter\InputFilterInterface
 */
trait SystemMessageTrait
{
    public function getSystemMessage(bool $asJson = false): string
    {
        if ($asJson) {
            $encoded = json_encode($this->getMessages()->jsonSerialize());

            return false === $encoded ? '[]' : $encoded;
        }

        /** @var array<array-key, string|array<array-key, string|array<array-key, string>>> $messages */
        $messages = $this->getMessages()->toArray();

        return implode('<br />', $this->flattenMessages($messages));
    }

    /**
     * Recursively flatten nested InputFilter error messages (e.g. from fieldsets,
     * or Laminas's own validator-key nesting) into a flat list of "field: message"
     * strings, so the user can tell which field a message belongs to.
     *
     * @param array<array-key, string|array<array-key, string|array<array-key, string>>> $messages
     * @return string[]
     */
    private function flattenMessages(array $messages, ?string $key = null): array
    {
        $flattened = [];
        foreach ($messages as $field => $message) {
            $label = null === $key ? (string) $field : "{$key}.{$field}";

            if (! is_array($message)) {
                $flattened[] = "{$label}: {$message}";
                continue;
            }

            if ($this->isLeafMessageSet($message)) {
                foreach ($message as $text) {
                    if (! is_string($text)) {
                        continue;
                    }

                    $flattened[] = "{$label}: {$text}";
                }

                continue;
            }

            foreach ($this->flattenMessages($message, $label) as $nested) {
                $flattened[] = $nested;
            }
        }

        return $flattened;
    }

    /**
     * A "leaf" message set is a validator-key => message string map
     * (e.g. `['isEmpty' => 'Value is required...']`) with no further nesting.
     *
     * @param array<array-key, mixed> $message
     */
    private function isLeafMessageSet(array $message): bool
    {
        foreach ($message as $value) {
            if (is_array($value)) {
                return false;
            }
        }

        return true;
    }
}
