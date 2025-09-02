<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class RegexCaster implements CasterInterface
{
    public function __construct(private string $pattern)
    {
        $this->assertValid($pattern);

        $this->pattern = $pattern;
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

    private function assertValid(string $pattern): void
    {
        set_error_handler(
            static function (int $severity, string $message) use ($pattern): bool {
                throw new CastException("Invalid regex '{$pattern}': {$message}");
            },
            E_WARNING
        );
        try {
            preg_match($pattern, '');
            if (function_exists('preg_last_error_msg')) {
                $err = preg_last_error();
                if ($err !== PREG_NO_ERROR) {
                    throw new CastException("Invalid regex '{$pattern}': " . preg_last_error_msg());
                }
            }
        } finally {
            restore_error_handler();
        }
    }
}
