<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;

final class JsonCaster implements CasterInterface
{
    public function __construct(private bool $assoc = true)
    {
    }
    public function cast(string $key, string $raw): mixed
    {
        return json_decode($raw, $this->assoc, 512, JSON_THROW_ON_ERROR);
    }
}
