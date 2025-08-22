<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Exception\ConstraintException;

final class IsExecutableConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): mixed
    {
        $p = self::path($value);
        if (! is_executable($p)) {
            throw new ConstraintException("path '{$p}' is not executable");
        }
        return $p;
    }
}
