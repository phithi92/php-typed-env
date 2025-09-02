<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

/**
 * Ensures that a string has at most a given number of characters.
 */
final class MaxLengthConstraint implements ConstraintInterface
{
    public function __construct(
        private int $max
    ) {
        if ($max < 0) {
            throw new ConstraintException('Maximum length cannot be negative.');
        }
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException('MaxLengthConstraint expects a string value.');
        }

        if (mb_strlen($value) > $this->max) {
            throw new ConstraintException(
                sprintf(
                    'String length is too long: expected at most %d characters, got %d.',
                    $this->max,
                    mb_strlen($value)
                )
            );
        }

        return $value;
    }
}
