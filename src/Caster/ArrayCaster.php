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

    private readonly bool $filterEmpty;

    public function __construct(string $delimiter, bool $filterEmpty = false)
    {
        if ($delimiter === '') {
            throw new CastException('Delimiter for ArrayCaster cannot be empty.');
        }

        $this->delimiter = $delimiter;
        $this->filterEmpty = $filterEmpty;
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

        return $this->splitAndFilterParts($key, $value);
    }

    /**
     * @return array<int, string>
     *
     * @throws CastException
     */
    private function splitAndFilterParts(string $key, string $value): array
    {
        $parts = array_map('trim', explode($this->delimiter, $value));

        if ($this->filterEmpty) {
            $parts = array_filter($parts, static fn ($v) => $v !== '');
        }

        if (count($parts) === 0) {
            throw new CastException("ENV {$key}: cannot cast '{$value}' to array (empty values only).");
        }

        return $parts;
    }
}
