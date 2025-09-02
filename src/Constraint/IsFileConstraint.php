<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Exception\ConstraintException;

final class IsFileConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): string
    {
        $p = self::path($value);
        if (! is_file($p)) {
            throw new ConstraintException("path '{$p}' is not a file");
        }
        return $p;
    }
}
