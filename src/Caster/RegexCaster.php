<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class RegexCaster implements CasterInterface
{
    public function __construct(private string $pattern)
    {
        if (@preg_match($pattern, '') !== 1) {
            throw new InvalidArgumentException("Invalid regex pattern: '{$pattern}'.");
        }
    }

    public function cast(string $key, string $raw): string
    {
        if (preg_match($this->pattern, $raw) !== 1) {
            throw new InvalidArgumentException(
                "Environment variable '{$key}' does not match pattern '{$this->pattern}'."
            );
        }
        return $raw;
    }
}
