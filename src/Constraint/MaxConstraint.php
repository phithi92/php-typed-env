<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class MaxConstraint implements ConstraintInterface
{
    public function __construct(private int|float $max)
    {
    }
    public function assert(string $key, mixed $value): mixed
    {
        if (! is_int($value) && ! is_float($value)) {
            throw new ConstraintException("ENV {$key}: max() expects a number");
        }
        if ($value > $this->max) {
            throw new ConstraintException("ENV {$key}: value {$value} > max {$this->max}");
        }
        return $value;
    }
}
