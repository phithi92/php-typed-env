<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class SizeCaster implements CasterInterface
{
    private const SIZE_REGEX = '/^(\d+)\s*(b|kb|mb|gb|tb)$/i';

    public function cast(string $key, string $raw): int
    {
        $trimmedRaw = $this->sanitizeRaw($key, $raw);

        if (preg_match('/^\d+$/', $trimmedRaw) === 1) {
            return (int) $trimmedRaw;
        }

        [$num, $unit] = $this->parseSize($trimmedRaw, $key, $raw);

        $mult = $this->unitMultiplier($key, $unit);

        $this->overflowProtect($key, $num, $mult);

        return $num * $mult;
    }

    private function unitMultiplier(string $key, string $unit): int
    {
        return match ($unit) {
            'b' => 1,
            'kb' => 1024,
            'mb' => 1024 ** 2,
            'gb' => 1024 ** 3,
            'tb' => 1024 ** 4,
            default => throw new InvalidArgumentException("ENV {$key}: unsupported size unit '{$unit}'"),
        };
    }

    /**
     * @return array{int,string}
     *
     * @throws InvalidArgumentException
     */
    private function parseSize(string $value, string $key, string $raw): array
    {
        if (preg_match(self::SIZE_REGEX, $value, $m) !== 1) {
            throw new InvalidArgumentException("ENV {$key}: '{$raw}' is not a valid size");
        }

        return [(int) $m[1],strtolower($m[2])];
    }

    private function sanitizeRaw(string $key, string $raw): string
    {
        $value = trim($raw);
        if ($value === '') {
            throw new InvalidArgumentException("ENV {$key}: size is empty");
        }

        return $value;
    }

    private function overflowProtect(string $key, int $num, int $mult): void
    {
        if ($num > intdiv(PHP_INT_MAX, $mult)) {
            throw new InvalidArgumentException("ENV {$key}: size overflows integer range");
        }
    }
}
