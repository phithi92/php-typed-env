<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use Locale;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class LocaleCaster implements CasterInterface
{
    public const FORMAT_POSIX = 'posix';   // de_DE
    public const FORMAT_BCP47 = 'bcp47';   // de-DE

    private const PATTERNS = [
        self::FORMAT_POSIX => '/^([a-z]{2})_([A-Z]{2})$/',                // de_DE
        self::FORMAT_BCP47 => '/^([a-z]{2,3})-([A-Z]{2}|\d{3})$/',        // de-DE, es-419
    ];

    private string $format;

    private string $displayLocale;

    public function __construct(string $format = self::FORMAT_POSIX, string $displayLocale = 'en')
    {
        if (! isset(self::PATTERNS[$format])) {
            throw new CastException(
                "Unsupported locale format '{$format}'. Use 'posix' (de_DE) or 'bcp47' (de-DE)."
            );
        }

        $this->format = $format;
        $this->displayLocale = $displayLocale;
    }

    public function cast(string $key, string $raw): string
    {
        $pattern = self::PATTERNS[$this->format];

        if (preg_match($pattern, $raw, $m) !== 1) {
            $hint = $this->format === self::FORMAT_POSIX
                ? "Use underscore '_' instead of '-' (e.g., de_DE, en_US)."
                : "Use dash '-' instead of '_' (e.g., de-DE, en-US).";

            throw new CastException(
                "Environment variable '{$key}' must match {$this->format} locale format. {$hint}"
            );
        }

        $lang = strtolower($m[1]);
        $region = strtoupper($m[2]);
        $icuLocale = "{$lang}_{$region}";

        return $this->validateWithIcu($key, $raw, $icuLocale, $lang, $region);
    }

    private function validateWithIcu(string $key, string $raw, string $icuLocale, string $lang, string $region): string
    {
        $dispLang = Locale::getDisplayLanguage($icuLocale, $this->displayLocale);
        $dispRegion = Locale::getDisplayRegion($icuLocale, $this->displayLocale);

        if ($dispLang === '' || $dispLang === $lang) {
            throw new CastException("Invalid language subtag '{$lang}' in '{$key}'.");
        }
        if ($dispRegion === '' || $dispRegion === $region) {
            throw new CastException("Invalid region subtag '{$region}' in '{$key}'.");
        }

        return $raw;
    }
}
