<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class UuidAnyCaster implements CasterInterface
{
    private const UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
    public function cast(string $key, string $raw): string
    {
        if (preg_match(self::UUID_REGEX, $raw) !== 1) {
            throw new CastException("ENV {$key}: '{$raw}' is not a valid UUID");
        }
        return $raw;
    }
}
