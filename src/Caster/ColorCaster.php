<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class ColorCaster implements CasterInterface
{
    private const HEX_REGEX =
        '/^#(?:[0-9A-Fa-f]{3}|[0-9A-Fa-f]{4}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/';

    private const RGB_REGEX =
        '/^rgb\('
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d),\s?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d),\s?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d)'
        . '\)$/';

    private const RGBA_REGEX =
        '/^rgba\('
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d),\s?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d),\s?'
        . '(?:25[0-5]|2[0-4]\d|1?\d?\d),\s?'
        . '(?:0|0?\.\d+|1(?:\.0)?)'
        . '\)$/';

    public function cast(string $key, string $raw): string
    {
        $value = trim($raw);

        // Immediately fail on empty values
        if ($value === '') {
            throw new CastException("Environment variable '{$key}' must not be empty.");
        }

        // HEX colors: quick prefix and length check before regex
        if ($value[0] === '#') {
            $len = strlen($value);
            if (
                ($len === 4 || $len === 5 || $len === 7 || $len === 9)
                && preg_match(self::HEX_REGEX, $value) === 1
            ) {
                return $value;
            }
        }

        // Fallback to RGB(A) patterns
        if (preg_match(self::RGB_REGEX, $value) === 1 || preg_match(self::RGBA_REGEX, $value) === 1) {
            return $value;
        }

        throw new CastException(
            "Environment variable '{$key}' must be a valid HEX (#RGB, #RGBA, #RRGGBB, #RRGGBBAA) or RGB(A) color."
        );
    }
}
