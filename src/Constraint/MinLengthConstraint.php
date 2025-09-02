<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * Ensures that a string has at least a given number of characters.
 */
final class MinLengthConstraint implements ConstraintInterface
{
    public function __construct(
        private int $min
    ) {
        if ($min < 0) {
            throw new ConstraintException('Minimum length cannot be negative.');
        }
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException('MinLengthConstraint expects a string value.');
        }

        $len = mb_strlen($value);

        if ($len < $this->min) {
            throw new ConstraintException(
                sprintf(
                    'String length is too short: expected at least %d characters, got %d.',
                    $this->min,
                    $len
                )
            );
        }

        return $value;
    }
}
