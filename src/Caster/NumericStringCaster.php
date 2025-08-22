<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class NumericStringCaster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        if (! ctype_digit($raw)) {
            throw new CastException("Environment variable '{$key}' must be numeric string.");
        }
        return $raw;
    }
}
