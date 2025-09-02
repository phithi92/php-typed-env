<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;

final class StringCaster implements CasterInterface
{
    public function cast(string $key, string $raw): string
    {
        return $raw;
    }
}
