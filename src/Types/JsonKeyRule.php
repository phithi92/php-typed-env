<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\JsonCaster;
use Phithi92\TypedEnv\Constraint\MaxItemsConstraint;
use Phithi92\TypedEnv\Constraint\MinItemsConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing JSON values.
 *
 * Examples of valid values:
 *  - '{"name":"John","age":30}'
 *  - '["apple","banana","cherry"]'
 *  - 'true'
 *  - 'null'
 */
final class JsonKeyRule extends KeyRule
{
    public function __construct(string $key, bool $asArray = true)
    {
        parent::__construct($key, new JsonCaster($asArray));
    }

    /**
     * Ensure the decoded JSON array/object has at least the given number of elements.
     */
    public function minItems(int $count): JsonKeyRule
    {
        return $this->addConstraint(new MinItemsConstraint($count));
    }

    /**
     * Ensure the decoded JSON array/object has at most the given number of elements.
     */
    public function maxItems(int $count): JsonKeyRule
    {
        return $this->addConstraint(new MaxItemsConstraint($count));
    }

    /**
     * Ensure the decoded JSON array/object has a size within the given bounds.
     */
    public function rangeItems(int $min, int $max): JsonKeyRule
    {
        return $this
            ->addConstraint(new MinItemsConstraint($min))
            ->addConstraint(new MaxItemsConstraint($max));
    }
}
