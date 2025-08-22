<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

abstract class AbstractPathConstraint implements ConstraintInterface
{
    protected static function path(mixed $v): string
    {
        if (! is_string($v) || $v === '') {
            throw new ConstraintException('path constraint expects non-empty string');
        }
        return $v;
    }
}
