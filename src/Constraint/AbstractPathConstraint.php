<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;

abstract class AbstractPathConstraint implements ConstraintInterface
{
    protected static function path(mixed $v): string
    {
        if (! is_string($v) || $v === '') {
            throw new InvalidArgumentException('path constraint expects non-empty string');
        }
        return $v;
    }
}
