<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class IpCaster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        if (filter_var($raw, FILTER_VALIDATE_IP) === false) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid IP");
        }
        return $raw;
    }
}
