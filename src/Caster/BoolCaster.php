<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class BoolCaster implements CasterInterface
{
    public function cast(string $key, string $raw): bool
    {
        if (ctype_digit($raw)) {
            return (int) $raw === 1;
        }

        $value = strtolower(trim($raw));

        $map = [
            '1' => true,
            '0' => false,
            'yes' => true,
            'no' => false,
            'true' => true,
            'false' => false,
            'on' => true,
            'off' => false,
        ];

        if (array_key_exists($value, $map)) {
            return $map[$value];
        }

        $res = filter_var($raw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        if ($res === null) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid bool");
        }

        return $res;
    }
}
