<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * @phpstan-type TldList list<string>
 */
final class UrlFilterTldsConstraint implements ConstraintInterface
{
    /** @var array<string> */
    private array $tlds;

    /** @var bool Whether the list acts as an allowlist (true) or denylist (false) */
    private bool $isAllowList;

    /**
     * @param list<string> $tlds       TLDs to allow or forbid (e.g., ['com', 'org', 'net'])
     * @param bool         $isAllowList True for allowlist mode, false for denylist mode
     */
    public function __construct(array $tlds, bool $isAllowList = true)
    {
        $this->tlds = array_map('strtolower', $tlds);
        $this->isAllowList = $isAllowList;
    }

    /**
     * Validate that the URL's TLD complies with the configured list.
     *
     * @param string $key   Environment variable name
     * @param mixed  $value Value to validate
     *
     * @return string The validated URL
     *
     * @throws ConstraintException If the value is invalid or violates the constraint
     */
    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: value must be a valid string.");
        }

        $host = parse_url($value, PHP_URL_HOST);
        $tld = $host !== false && $host !== null
            ? strtolower(pathinfo($host, PATHINFO_EXTENSION))
            : '';

        if ($tld === '') {
            throw new ConstraintException("ENV {$key}: URL has no valid TLD.");
        }

        $existsInList = in_array($tld, $this->tlds, true);

        if ($this->isAllowList && ! $existsInList) {
            throw new ConstraintException(sprintf(
                'The URL for "%s" has an invalid TLD ".%s". Allowed TLDs: %s',
                $key,
                $tld,
                implode(', ', $this->tlds)
            ));
        }

        if (! $this->isAllowList && $existsInList) {
            throw new ConstraintException(sprintf(
                'The URL for "%s" uses a forbidden TLD ".%s". Forbidden TLDs: %s',
                $key,
                $tld,
                implode(', ', $this->tlds)
            ));
        }

        return $value;
    }
}
