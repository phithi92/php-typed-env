<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class HexCaster implements CasterInterface
{
    public function __construct(private ?int $length = null)
    {
    }

    public function cast(string $key, string $raw): string
    {
        if (! ctype_xdigit($raw)) {
            throw new InvalidArgumentException("Environment variable '{$key}' must be a valid hex string.");
        }

        if ($this->length !== null && strlen($raw) !== $this->length) {
            throw new InvalidArgumentException(
                "Environment variable '{$key}' must be exactly {$this->length} characters long."
            );
        }

        return strtolower($raw);
    }
}
