<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class NoEmptyValuesConstraint implements ConstraintInterface
{
    public function assert(string $key, mixed $values): mixed
    {
        if (! is_array($values) || $values === []) {
            return $values;
        }

        foreach ($values as $value) {
            if ($value === '') {
                throw new ConstraintException('Empty values are not allowed');
            }
        }

        return $values;
    }
}
