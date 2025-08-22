<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Exception\ConstraintException;

final class IsWritableConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): mixed
    {
        $p = self::path($value);
        if (! is_writable($p)) {
            throw new ConstraintException("path '{$p}' is not writable");
        }
        return $p;
    }
}
