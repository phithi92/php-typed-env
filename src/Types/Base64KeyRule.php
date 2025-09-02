<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\Base64Caster;
use Phithi92\TypedEnv\Constraint\MaxLengthConstraint;
use Phithi92\TypedEnv\Constraint\MinLengthConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing Base64-encoded values.
 *
 * Examples of valid values:
 *  - "SGVsbG8gd29ybGQ="   => "Hello world"
 *  - "YWJjZGVmZw=="       => "abcdefg"
 *  - "QVBJX1RPS0VOX1ZBTFVF" => "API_TOKEN_VALUE"
 */
final class Base64KeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new Base64Caster());
    }

    /**
     * Ensure the Base64 string has at least the given length.
     */
    public function minLength(int $length): KeyRule
    {
        return $this->addConstraint(new MinLengthConstraint($length));
    }

    /**
     * Ensure the Base64 string has at most the given length.
     */
    public function maxLength(int $length): KeyRule
    {
        return $this->addConstraint(new MaxLengthConstraint($length));
    }

    /**
     * Ensure the Base64 string length is within the given bounds.
     */
    public function rangeLength(int $min, int $max): KeyRule
    {
        return $this
            ->addConstraint(new MinLengthConstraint($min))
            ->addConstraint(new MaxLengthConstraint($max));
    }
}
