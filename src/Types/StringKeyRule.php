<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\StringCaster;
use Phithi92\TypedEnv\Constraint\EnumConstraint;
use Phithi92\TypedEnv\Constraint\MaxLengthConstraint;
use Phithi92\TypedEnv\Constraint\MinLengthConstraint;
use Phithi92\TypedEnv\Constraint\PatternConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables that are strings.
 */
final class StringKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new StringCaster());
    }

    /**
     * Ensure the string matches the given regular expression.
     *
     * @param string $regex Regular expression pattern
     */
    public function pattern(string $regex): StringKeyRule
    {
        return $this->addConstraint(new PatternConstraint($regex));
    }

    /**
     * Ensure the string has at least the given number of characters.
     *
     * @param int $length Minimum length
     */
    public function minLength(int $length): StringKeyRule
    {
        return $this->addConstraint(new MinLengthConstraint($length));
    }

    /**
     * Ensure the string has at most the given number of characters.
     *
     * @param int $length Maximum length
     */
    public function maxLength(int $length): StringKeyRule
    {
        return $this->addConstraint(new MaxLengthConstraint($length));
    }

    /**
     * Ensure the string length is within the given range.
     *
     * @param int $min Minimum length
     * @param int $max Maximum length
     */
    public function lengthBetween(int $min, int $max): StringKeyRule
    {
        return $this
            ->addConstraint(new MinLengthConstraint($min))
            ->addConstraint(new MaxLengthConstraint($max));
    }

    /**
     * Restrict the string to a fixed set of allowed values.
     *
     * @param list<string> $values Allowed string values
     */
    public function enum(array $values): StringKeyRule
    {
        return $this->addConstraint(new EnumConstraint($values));
    }
}
