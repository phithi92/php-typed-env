<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\ArrayCaster;
use Phithi92\TypedEnv\Constraint\MaxItemsConstraint;
use Phithi92\TypedEnv\Constraint\MinItemsConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing arrays (comma-separated or custom delimiter).
 *
 * Examples:
 *  - "apple,banana,cherry" => ["apple", "banana", "cherry"]
 *  - "1,2,3"               => ["1", "2", "3"]
 *  - "alpha"               => ["alpha"]
 */
final class ArrayKeyRule extends KeyRule
{
    public function __construct(string $key, string $delimiter = ',')
    {
        parent::__construct($key, new ArrayCaster($delimiter));
    }

    /**
     * Ensure the array has at least the given number of items.
     */
    public function minItems(int $count): ArrayKeyRule
    {
        return $this->addConstraint(new MinItemsConstraint($count));
    }

    /**
     * Ensure the array has at most the given number of items.
     */
    public function maxItems(int $count): ArrayKeyRule
    {
        return $this->addConstraint(new MaxItemsConstraint($count));
    }

    /**
     * Ensure the array size is within the given range.
     */
    public function rangeItems(int $min, int $max): ArrayKeyRule
    {
        return $this
            ->addConstraint(new MinItemsConstraint($min))
            ->addConstraint(new MaxItemsConstraint($max));
    }
}
