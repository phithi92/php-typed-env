<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class LocaleCaster implements CasterInterface
{
    private const LOCALE_REGEX = '/^[a-z]{2}_[A-Z]{2}$/';

    public function cast(string $key, string $raw): string
    {
        if (preg_match(self::LOCALE_REGEX, $raw) !== 1) {
            throw new InvalidArgumentException(
                "Environment variable '{$key}' must be a valid locale (e.g., en_US, de_DE)."
            );
        }
        return $raw;
    }
}
