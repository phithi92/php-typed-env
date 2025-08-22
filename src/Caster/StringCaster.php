<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class StringCaster implements CasterInterface
{
    public function cast(string $key, mixed $raw): string
    {
        if (! is_string($raw)) {
            throw new CastException(
                sprintf("ENV '%s': Expected a string value, got %s.", $key, gettype($raw))
            );
        }

        return $raw;
    }
}
