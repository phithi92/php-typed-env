<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class RegexCaster implements CasterInterface
{
    public function __construct(private string $pattern)
    {
        RegexUtil::assertValid($pattern);
    }

    public function cast(string $key, string $raw): string
    {
        if (preg_match($this->pattern, $raw) !== 1) {
            throw new CastException(
                "Environment variable '{$key}' does not match pattern '{$this->pattern}'."
            );
        }
        return $raw;
    }
}
