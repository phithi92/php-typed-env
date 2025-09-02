<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class ListCaster implements CasterInterface
{
    private readonly string $delimiter;
    private readonly bool $allowEmpty;

    public function __construct(string $delimiter, bool $allowEmpty)
    {
        if ($delimiter === '') {
            throw new CastException('Delimiter must not be empty');
        }

        $this->delimiter = $delimiter;
        $this->allowEmpty = $allowEmpty;
    }

    /** @return list<string> */
    public function cast(string $key, string $raw): array
    {
        $data = explode($this->delimiter, $raw);
        if ($data === []) {
            return $data;
        }

        $parts = array_map('trim', $data);
        if (! $this->allowEmpty) {
            $parts = array_values(array_filter($parts, static fn ($x) => $x !== ''));
        }

        return $parts;
    }
}
