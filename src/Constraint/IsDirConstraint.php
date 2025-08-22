<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Exception\ConstraintException;

final class IsDirConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): mixed
    {
        $p = self::path($value);
        if (! is_dir($p)) {
            throw new ConstraintException("path '{$p}' is not a directory");
        }
        return $p;
    }
}
