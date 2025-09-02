<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class MinItemsConstraint implements ConstraintInterface
{
    public function __construct(private int|float $min)
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
        if ($items < $this->min) {
            throw new ConstraintException("ENV {$key}: item count {$items} < min {$this->min}");
        }
        return $value;
    }
}
