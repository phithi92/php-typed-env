<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\HexCaster;
use Phithi92\TypedEnv\Constraint\MaxLengthConstraint;
use Phithi92\TypedEnv\Constraint\MinLengthConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing hexadecimal values.
 *
 * Examples of valid values:
 *  - "a3f5"
 *  - "deadbeef"
 *  - "0123456789abcdef"
 */
final class HexKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new HexCaster());
    }

    /**
     * Ensure the hex value has at least the given length.
     */
    public function minLength(int $length): HexKeyRule
    {
        return $this->addConstraint(new MinLengthConstraint($length));
    }

    /**
     * Ensure the hex value has at most the given length.
     */
    public function maxLength(int $length): HexKeyRule
    {
        return $this->addConstraint(new MaxLengthConstraint($length));
    }

    /**
     * Ensure the hex value length is within the given bounds.
     */
    public function rangeLength(int $min, int $max): HexKeyRule
    {
        return $this
            ->addConstraint(new MinLengthConstraint($min))
            ->addConstraint(new MaxLengthConstraint($max));
    }
}
