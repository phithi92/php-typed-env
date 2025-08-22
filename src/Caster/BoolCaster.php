<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class BoolCaster implements CasterInterface
{
    public function cast(string $key, string $raw): bool
    {
        $res = filter_var($raw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($res === null) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid bool");
        }
        return $res;
    }
}
