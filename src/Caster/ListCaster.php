<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class ListCaster implements CasterInterface
{
    private readonly string $delimiter;

    public function __construct(string $delimiter = ',', private bool $allowEmpty = false)
    {
        if ($delimiter === '') {
            throw new CastException('Delimiter must not be empty');
        }

        $this->delimiter = $delimiter;
    }

    /** @return list<string> */
    public function cast(string $key, string $raw): array
    {
        $parts = array_map('trim', explode($this->delimiter, $raw));
        if (! $this->allowEmpty) {
            $parts = array_values(array_filter($parts, static fn ($x) => $x !== ''));
        }
        return $parts;
    }
}
