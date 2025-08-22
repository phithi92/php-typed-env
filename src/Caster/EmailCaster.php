<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class EmailCaster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        if (filter_var($raw, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid email");
        }
        return $raw;
    }
}
