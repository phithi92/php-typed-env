<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class PortCaster implements CasterInterface
{
    public function cast(string $key, string $raw): int
    {
        if ($raw === '' || ! ctype_digit($raw)) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid port");
        }

        $p = (int) $raw;
        if ($p < 1 || $p > 65535) {
            throw new InvalidArgumentException("ENV {$key}: port {$p} out of range 1..65535");
        }

        return $p;
    }
}
