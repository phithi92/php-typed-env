<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class VersionCaster implements CasterInterface
{
    /**
     * Regex pattern for validating Semantic Versioning (SemVer 2.0.0) strings
     * in compliance with RFC 3986. It matches standard versions, pre-release
     * identifiers, and optional build metadata, ensuring strict SemVer syntax.
     */
    private const VERSION_PATTERN = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)'
        . '(?:-((?:0|[1-9A-Za-z-][0-9A-Za-z-]*)(?:\.(?:0|[1-9A-Za-z-][0-9A-Za-z-]*))*))?'
        . '(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/';

    public function cast(string $key, string $raw): string
    {
        if (preg_match(self::VERSION_PATTERN, $raw) !== 1) {
            throw new InvalidArgumentException(
                "Environment variable '{$key}' must be a valid semantic version (compliant with SemVer 2.0.0)."
            );
        }
        return $raw;
    }
}
