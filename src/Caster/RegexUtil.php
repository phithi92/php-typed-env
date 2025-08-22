<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;

final class RegexUtil
{
    /** Validate PCRE without emitting warnings. */
    public static function assertValid(string $pattern): void
    {
        set_error_handler(
            static function (int $severity, string $message) use ($pattern): bool {
                throw new InvalidArgumentException("Invalid regex '{$pattern}': {$message}");
            },
            E_WARNING
        );
        try {
            preg_match($pattern, '');
            if (function_exists('preg_last_error_msg')) {
                $err = preg_last_error();
                if ($err !== PREG_NO_ERROR) {
                    throw new InvalidArgumentException("Invalid regex '{$pattern}': " . preg_last_error_msg());
                }
            }
        } finally {
            restore_error_handler();
        }
    }
}
