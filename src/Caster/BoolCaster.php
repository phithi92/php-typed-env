<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class BoolCaster implements CasterInterface
{
    public function cast(string $key, string $raw): bool
    {
        $res = filter_var($raw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($res === null) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid bool");
        }
        return $res;
    }
}
