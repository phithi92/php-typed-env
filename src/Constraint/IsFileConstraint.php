<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;

final class IsFileConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): mixed
    {
        $p = self::path($value);
        if (! is_file($p)) {
            throw new InvalidArgumentException("path '{$p}' is not a file");
        }
        return $p;
    }
}
