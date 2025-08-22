<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class Base64Caster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        $decoded = base64_decode($raw, true);

        if ($decoded === false || base64_encode($decoded) !== $raw) {
            throw new InvalidArgumentException(
                "Environment variable '{$key}' must be a valid Base64 encoded string."
            );
        }

        return $raw;
    }
}
