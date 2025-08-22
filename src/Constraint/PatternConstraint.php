<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;
use Phithi92\TypedEnv\Caster\RegexUtil;
use Phithi92\TypedEnv\Contracts\ConstraintInterface;

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
            throw new InvalidArgumentException("ENV {$key}: '{$s}' does not match pattern {$this->regex}");
        }
        return $value;
    }
}
