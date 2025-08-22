<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class Base64Caster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        $decoded = base64_decode($raw, true);

        if ($decoded === false || base64_encode($decoded) !== $raw) {
            throw new CastException(
                "Environment variable '{$key}' must be a valid Base64 encoded string."
            );
        }

        return $raw;
    }
}
