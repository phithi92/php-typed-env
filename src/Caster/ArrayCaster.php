<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

/**
 * Converts a delimited string into an array of strings.
 *
 * Examples:
 *   "a,b,c"  => ["a", "b", "c"]
 *   "  x | y | z " with delimiter "|" => ["x", "y", "z"]
 */
final class ArrayCaster implements CasterInterface
{
    private readonly string $delimiter;

    public function __construct(string $delimiter = ',')
    {
        if ($delimiter === '') {
            throw new CastException('Delimiter for ArrayCaster cannot be empty.');
        }
        $this->delimiter = $delimiter;
    }

    /**
     * Cast the raw value into an array of strings.
     *
     * @param string $key   Environment variable name
     * @param string $value Raw string value
     *
     * @return array<int, string>
     */
    public function cast(string $key, string $value): array
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return [];
        }

        return $this->filteredValues($key, $value);
    }

    /**
     * @return array<int, string>
     *
     * @throws CastException
     */
    private function filteredValues(string $key, string $value): array
    {
        $parts = array_map('trim', explode($this->delimiter, $value));

        $filtered = array_filter($parts, static fn ($v) => $v !== '');
        if (count($filtered) === 0) {
            throw new CastException("ENV {$key}: cannot cast '{$value}' to array (empty values only).");
        }

        return $filtered;
    }
}
