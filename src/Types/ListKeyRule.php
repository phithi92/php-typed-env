<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\ListCaster;
use Phithi92\TypedEnv\Constraint\EnumListConstraint;
use Phithi92\TypedEnv\Constraint\MaxItemsConstraint;
use Phithi92\TypedEnv\Constraint\MinItemsConstraint;
use Phithi92\TypedEnv\Constraint\NoEmptyValuesConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing lists (comma-separated values).
 *
 * Examples of valid values:
 *  - "a,b,c"   => ["a", "b", "c"]
 *  - "1,2,3"   => ["1", "2", "3"]
 *  - "apple"   => ["apple"]
 */
final class ListKeyRule extends KeyRule
{
    public function __construct(string $key, string $delimiter = ',', bool $filterEmpty = false)
    {
        parent::__construct($key, new ListCaster($delimiter, $filterEmpty));
    }

    /**
     * Ensure the list has at least the given number of items.
     */
    public function minItems(int $count): self
    {
        return $this->addConstraint(new MinItemsConstraint($count));
    }

    /**
     * Ensure the list has at most the given number of items.
     */
    public function maxItems(int $count): self
    {
        return $this->addConstraint(new MaxItemsConstraint($count));
    }

    /**
     * Ensure the list has between the given number of items.
     */
    public function rangeItems(int $min, int $max): self
    {
        return $this
            ->addConstraint(new MinItemsConstraint($min))
            ->addConstraint(new MaxItemsConstraint($max));
    }

    public function assertValuesNotEmpty(): self
    {
        return $this->addConstraint(new NoEmptyValuesConstraint());
    }

    /**
     * @param list<string> $values
     */
    public function allowedValues(array $values): self
    {
        return $this->addConstraint(new EnumListConstraint($values));
    }
}
