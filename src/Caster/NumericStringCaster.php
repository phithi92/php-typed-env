<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class NumericStringCaster implements CasterInterface
{
    /**
     * Whether to allow an optional sign (+/-) in front of the digits.
     */
    private bool $allowSign;

    public function __construct(bool $allowSign = true)
    {
        $this->allowSign = $allowSign;
    }

    public function cast(string $key, string $raw): string
    {
        $value = trim($raw);

        if ($value === '') {
            throw new CastException("Environment variable '{$key}' must not be empty.");
        }

        $pattern = $this->allowSign ? '/^[+-]?\d+$/' : '/^\d+$/';

        // preg_match returns 1 (match), 0 (no match), false (error) → we require 1
        if (preg_match($pattern, $value) !== 1) {
            throw new CastException("Environment variable '{$key}' must be a numeric string, got '{$raw}'.");
        }

        return $value;
    }
}
