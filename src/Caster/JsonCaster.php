<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use JsonException;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class JsonCaster implements CasterInterface
{
    public function __construct(private bool $assoc)
    {
    }
    public function cast(string $key, string $raw): mixed
    {
        try {
            return json_decode($raw, $this->assoc, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $shortRaw = mb_substr($raw, 0, 50) . (mb_strlen($raw) > 50 ? '...' : '');
            throw new CastException(
                sprintf(
                    "ENV '%s': Invalid JSON. Error: %s. Input (truncated): %s",
                    $key,
                    $e->getMessage(),
                    $shortRaw
                ),
                0,
                $e
            );
        }
    }
}
