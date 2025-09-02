<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\FloatCaster;
use Phithi92\TypedEnv\Constraint\MaxConstraint;
use Phithi92\TypedEnv\Constraint\MinConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables that must be floats.
 */
final class FloatKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new FloatCaster());
    }

    /**
     * Ensure the float value is greater than or equal to the given minimum.
     */
    public function min(float $value): FloatKeyRule
    {
        return $this->addConstraint(new MinConstraint($value));
    }

    /**
     * Ensure the float value is less than or equal to the given maximum.
     */
    public function max(float $value): FloatKeyRule
    {
        return $this->addConstraint(new MaxConstraint($value));
    }
}
