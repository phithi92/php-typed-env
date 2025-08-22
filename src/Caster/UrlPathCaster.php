<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class UrlPathCaster implements CasterInterface
{
    private const URL_PATH_REGEX = '/^(\/[A-Za-z0-9._~\-\/%]*)$/';

    public function cast(string $key, string $raw): string
    {
        if (preg_match(self::URL_PATH_REGEX, $raw) !== 1) {
            throw new InvalidArgumentException(
                "Environment variable '{$key}' must be a valid URL path (starting with '/')."
            );
        }

        return $raw;
    }
}
