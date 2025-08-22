<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class FloatCaster implements CasterInterface
{
    public function cast(string $key, string $raw): float
    {
        $s = trim($raw);
        if ($s === '' || ! is_numeric($s)) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid float");
        }
        return (float) $s;
    }
}
