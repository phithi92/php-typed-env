<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\NumericStringCaster;
use Phithi92\TypedEnv\Constraint\MaxLengthConstraint;
use Phithi92\TypedEnv\Constraint\MinLengthConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables that must be numeric strings.
 *
 * Examples of valid values:
 *  - "12345"
 *  - "000123"
 *  - "9876543210"
 */
final class NumericStringKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new NumericStringCaster());
    }

    /**
     * Ensure the string has at least the given number of digits.
     */
    public function minLength(int $length): NumericStringKeyRule
    {
        return $this->addConstraint(new MinLengthConstraint($length));
    }

    /**
     * Ensure the string has at most the given number of digits.
     */
    public function maxLength(int $length): NumericStringKeyRule
    {
        return $this->addConstraint(new MaxLengthConstraint($length));
    }

    /**
     * Ensure the string length is within the given range.
     */
    public function rangeLength(int $min, int $max): NumericStringKeyRule
    {
        return $this
            ->addConstraint(new MinLengthConstraint($min))
            ->addConstraint(new MaxLengthConstraint($max));
    }
}
