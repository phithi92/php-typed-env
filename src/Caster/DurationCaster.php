<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use DateInterval;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class DurationCaster implements CasterInterface
{
    private const DURATION_REGEX = '/^(\d+(?:\.\d+)?)(ms|s|m|h|d)?$/i';
    private bool $returnInterval;
    private string $roundingMode;

    public function __construct(bool $returnInterval, string $roundingMode)
    {
        if (! in_array($roundingMode, ['floor', 'ceil', 'round'], true)) {
            throw new CastException("Invalid rounding mode: {$roundingMode}");
        }

        $this->returnInterval = $returnInterval;
        $this->roundingMode = $roundingMode;
    }

    /**
     * @param string $key Environment variable name
     * @param string $raw Environment variable raw value
     */
    public function cast(string $key, string $raw): int|DateInterval
    {
        $trimmed = trim($raw);

        if (preg_match(self::DURATION_REGEX, $trimmed, $matches) !== 1) {
            throw new CastException(
                "ENV {$key}: '{$raw}' is not a valid duration (e.g., '1500ms', '30s', '5m')."
            );
        }

        $number = (float) $matches[1];
        $unit = strtolower($matches[2] ?? 's');

        $secondsFloat = match ($unit) {
            'ms' => $number / 1000,
            's' => $number,
            'm' => $number * 60,
            'h' => $number * 3600,
            'd' => $number * 86400,
            default => throw new CastException(
                "ENV {$key}: '{$unit}' is not a supported duration unit."
            ),
        };

        $seconds = $this->applyRounding($secondsFloat);

        if ($this->returnInterval) {
            return new DateInterval('PT' . $seconds . 'S');
        }

        return $seconds; // int seconds
    }

    private function applyRounding(float $seconds): int
    {
        return match ($this->roundingMode) {
            'ceil' => (int) ceil($seconds),
            'round' => (int) round($seconds),
            default => (int) floor($seconds),
        };
    }
}
