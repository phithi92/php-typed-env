<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;

final class IsExecutableConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): mixed
    {
        $p = self::path($value);
        if (! is_executable($p)) {
            throw new InvalidArgumentException("path '{$p}' is not executable");
        }
        return $p;
    }
}
