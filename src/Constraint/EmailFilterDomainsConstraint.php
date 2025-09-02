<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * @phpstan-type DomainList list<string>
 */
final class EmailFilterDomainsConstraint implements ConstraintInterface
{
    /** @var array<string> */
    private array $domains;

    /** @var bool Whether the list is allowlist (true) or denylist (false) */
    private bool $isAllowList;

    /**
     * @param list<string> $domains     Domains to allow or forbid
     * @param bool         $isAllowList True for allowlist mode, false for denylist mode
     */
    public function __construct(array $domains, bool $isAllowList = true)
    {
        $this->domains = array_map('strtolower', $domains);
        $this->isAllowList = $isAllowList;
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: value must be a valid string.");
        }

        $parts = explode('@', $value);
        $domain = strtolower($parts[1] ?? '');

        $existsInList = in_array($domain, $this->domains, true);

        if ($this->isAllowList && ! $existsInList) {
            throw new ConstraintException(sprintf(
                'The email for "%s" has an invalid domain "%s". Allowed domains: %s',
                $key,
                $domain,
                implode(', ', $this->domains)
            ));
        }

        if (! $this->isAllowList && $existsInList) {
            throw new ConstraintException(sprintf(
                'The email for "%s" uses a forbidden domain "%s". Forbidden domains: %s',
                $key,
                $domain,
                implode(', ', $this->domains)
            ));
        }

        return $value;
    }
}
