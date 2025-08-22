<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class IpCaster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        if (filter_var($raw, FILTER_VALIDATE_IP) === false) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid IP");
        }
        return $raw;
    }
}
