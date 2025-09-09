<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;
use Phithi92\TypedEnv\Exception\CastException;

final class UrlCaster implements CasterInterface
{
    private const DEFAULT_PROTOCOLS = ['http', 'https', 'ftp'];
    /** @var array<string> */
    private array $protocols;

    /**
     * @param array<string>|null $protocols  erlaubte Protokolle (z. B. ['http','https','ftp'])
     */
    public function __construct(?array $protocols = null)
    {
        $protocols = $protocols ?? self::DEFAULT_PROTOCOLS;

        // Normalisieren + validieren
        $normalized = [];
        foreach ($protocols as $p) {
            if (! is_string($p) || $p === '') {
                throw new InvalidArgumentException('Protocol names must be non-empty strings.');
            }
            $normalized[] = strtolower($p);
        }
        $normalized = array_values(array_unique($normalized));
        if ($normalized === []) {
            throw new InvalidArgumentException('At least one allowed protocol must be specified.');
        }

        $this->protocols = $normalized;
    }

    public function cast(string $key, string $raw): string
    {
        // 1. Basic URL validation using PHP's filter_var
        $url = filter_var($raw, FILTER_VALIDATE_URL);
        if ($url === false) {
            throw new CastException("Invalid URL: {$raw}");
        }

        // 2. Parse the validated URL into components
        $parts = parse_url($url);
        if ($parts === false) {
            throw new CastException("Invalid URL: {$raw}");
        }

        $scheme = $parts['scheme'] ?? null;
        $host = $parts['host'] ?? null;

        // 3. Require both scheme and host to be present and non-empty
        if (! is_string($scheme) || $scheme === '' || ! is_string($host) || $host === '') {
            throw new CastException("URL must include scheme and host: {$raw}");
        }

        // 4. Enforce allowed protocols (case-insensitive)
        $scheme = strtolower($scheme);
        if (! in_array($scheme, $this->protocols, true)) {
            throw new CastException(
                "Scheme '{$scheme}' not allowed. Allowed: " . implode(', ', $this->protocols)
            );
        }

        // 5. Return the validated and normalized URL (not the raw input)
        return $url;
    }
}
