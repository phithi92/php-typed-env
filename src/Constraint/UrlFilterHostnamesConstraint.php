<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class UrlFilterHostnamesConstraint implements ConstraintInterface
{
    /** @var array<string> */
    private array $hosts;

    /** @var bool Whether the list acts as an allowlist (true) or denylist (false) */
    private bool $isAllowList;

    /**
     * @param list<string> $hosts       Hostnames to allow or forbid
     * @param bool         $isAllowList True for allowlist mode, false for denylist mode
     */
    public function __construct(array $hosts, bool $isAllowList = true)
    {
        $this->hosts = array_map('strtolower', $hosts);
        $this->isAllowList = $isAllowList;
    }

    /**
     * Validate that the URL's hostname complies with the configured list.
     *
     * @param string $key   Environment key name
     * @param mixed  $value Value to validate
     *
     * @return string The validated URL
     *
     * @throws ConstraintException If the value is not a valid string or violates the constraint
     */
    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: value must be a valid string.");
        }

        $host = parse_url($value, PHP_URL_HOST);
        $host = is_string($host) ? strtolower($host) : null;

        if ($host === null) {
            throw new ConstraintException("ENV {$key}: URL has no valid hostname.");
        }

        $existsInList = in_array($host, $this->hosts, true);

        if ($this->isAllowList && ! $existsInList) {
            throw new ConstraintException(sprintf(
                'The URL for "%s" has an invalid host "%s". Allowed hosts: %s',
                $key,
                $host,
                implode(', ', $this->hosts)
            ));
        }

        if (! $this->isAllowList && $existsInList) {
            throw new ConstraintException(sprintf(
                'The URL for "%s" uses a forbidden host "%s". Forbidden hosts: %s',
                $key,
                $host,
                implode(', ', $this->hosts)
            ));
        }

        return $value;
    }
}
