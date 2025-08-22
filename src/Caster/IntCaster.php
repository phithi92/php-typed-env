<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class IntCaster implements CasterInterface
{
    private const INT_REGEX = '/^-?\d+$/';

    public function cast(string $key, string $raw): int
    {
        $s = trim($raw);
        if ($s === '' || preg_match(self::INT_REGEX, $s) !== 1) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid int");
        }
        return (int) $s;
    }
}
