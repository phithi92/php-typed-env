<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class EmailFilterTldsConstraint implements ConstraintInterface
{
    /** @var array<string> */
    private array $tlds;

    /** @var bool Whether the list is allowlist (true) or denylist (false) */
    private bool $isAllowList;

    /**
     * @param array<string> $tlds       List of TLDs to allow or forbid
     * @param bool     $isAllowList True for allowlist mode, false for denylist
     */
    public function __construct(array $tlds, bool $isAllowList = true)
    {
        $this->tlds = array_map('strtolower', $tlds);
        $this->isAllowList = $isAllowList;
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: no valid string value.");
        }

        $parts = explode('@', $value);
        $domain = $parts[1] ?? '';
        $tld = strtolower(pathinfo($domain, PATHINFO_EXTENSION));

        $existsInList = in_array($tld, $this->tlds, true);

        if ($this->isAllowList && ! $existsInList) {
            throw new ConstraintException(sprintf(
                'The email for "%s" has an invalid TLD ".%s". Allowed TLDs: %s',
                $key,
                $tld,
                implode(', ', $this->tlds)
            ));
        }

        if (! $this->isAllowList && $existsInList) {
            throw new ConstraintException(sprintf(
                'The email for "%s" has a forbidden TLD ".%s". Forbidden TLDs: %s',
                $key,
                $tld,
                implode(', ', $this->tlds)
            ));
        }

        return $value;
    }
}
