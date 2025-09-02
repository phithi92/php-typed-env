<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * Deny (blacklist) specific IP addresses.
 *
 * Example:
 *   new IpDenyListConstraint(['127.0.0.1', '10.0.0.1'])
 */
final class IpDenyListConstraint implements ConstraintInterface
{
    /** @var list<string> */
    private array $denied;

    /**
     * @param list<string> $denied
     */
    public function __construct(array $denied)
    {
        $this->denied = array_map('strval', $denied);
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: expected IP string, got " . gettype($value));
        }

        if (in_array($value, $this->denied, true)) {
            throw new ConstraintException("ENV {$key}: {$value} is in the deny list.");
        }

        return $value;
    }
}
