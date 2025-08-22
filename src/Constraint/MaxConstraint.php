<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;

final class MaxConstraint implements ConstraintInterface
{
    public function __construct(private int|float $max)
    {
    }
    public function assert(string $key, mixed $value): mixed
    {
        if (! is_int($value) && ! is_float($value)) {
            throw new InvalidArgumentException("ENV {$key}: max() expects a number");
        }
        if ($value > $this->max) {
            throw new InvalidArgumentException("ENV {$key}: value {$value} > max {$this->max}");
        }
        return $value;
    }
}
