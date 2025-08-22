<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Caster\RegexUtil;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class PatternConstraint implements ConstraintInterface
{
    public function __construct(private string $regex)
    {
        RegexUtil::assertValid($regex);
    }
    public function assert(string $key, mixed $value): mixed
    {
        $s = is_scalar($value) ? (string) $value : '';
        if (preg_match($this->regex, $s) !== 1) {
            throw new ConstraintException("ENV {$key}: '{$s}' does not match pattern {$this->regex}");
        }
        return $value;
    }
}
