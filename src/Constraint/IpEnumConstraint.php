<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class IpEnumConstraint implements ConstraintInterface
{
    /** @var list<string> */
    private array $allowed;

    /**
     * @param list<string> $allowed
     */
    public function __construct(array $allowed)
    {
        $this->allowed = array_map('strval', $allowed);
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: expected IP string, got " . gettype($value));
        }

        if (! in_array($value, $this->allowed, true)) {
            throw new ConstraintException("ENV {$key}: {$value} is not in the allowed IP list.");
        }

        return $value;
    }
}
