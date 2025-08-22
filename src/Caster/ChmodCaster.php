<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class ChmodCaster implements CasterInterface
{
    private const CHMOD_REGEX = '/^[0-7]{3,4}$/';
    /**
     * Converts a chmod string like "755" or "0755" into an integer with octal semantics.
     *
     * @param string $key  The env key name (for error messages).
     * @param string $raw  The raw chmod value from .env (e.g., "755", "0644").
     *
     * @return int         Integer representing the octal permission mask.
     *
     * @throws InvalidArgumentException if the value is invalid.
     */
    public function cast(string $key, string $raw): int
    {
        $trimmed = trim($raw);

        // Must be 3 or 4 octal digits
        if (preg_match(self::CHMOD_REGEX, $trimmed) !== 1) {
            throw new InvalidArgumentException("Env {$key}: invalid chmod value '{$raw}'");
        }

        // Interpret as octal
        return intval($trimmed, 8);
    }
}
