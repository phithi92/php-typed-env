<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class ColorCaster implements CasterInterface
{
    private const RGB_REGEX =
        '/^rgb\('
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d), ?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d), ?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d)'
        . '\)$/';

    private const RGBA_REGEX =
        '/^rgba\('
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d), ?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d), ?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d), ?'
        . '(?:0|0?\.\d+|1(?:\.0)?)'
        . '\)$/';

    public function cast(string $key, string $raw): string
    {
        if (preg_match(self::RGB_REGEX, $raw) !== 1 && preg_match(self::RGBA_REGEX, $raw) !== 1) {
            throw new InvalidArgumentException("Environment variable '{$key}' must be a valid HEX or RGB(A) color.");
        }

        return $raw;
    }
}
