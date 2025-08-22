<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class FloatCaster implements CasterInterface
{
    public function cast(string $key, string $raw): float
    {
        $s = trim($raw);
        if ($s === '' || ! is_numeric($s)) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid float");
        }
        return (float) $s;
    }
}
