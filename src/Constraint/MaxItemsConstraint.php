<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class MaxItemsConstraint implements ConstraintInterface
{
    public function __construct(private int|float $max)
    {
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_array($value)) {
            throw new ConstraintException(
                sprintf(
                    "ENV {$key}: unexpected value type %s",
                    gettype($value)
                )
            );
        }

        $items = count($value);
        if ($items > $this->max) {
            throw new ConstraintException("ENV {$key}: items count {$items} > max {$this->max}");
        }

        return $value;
    }
}
