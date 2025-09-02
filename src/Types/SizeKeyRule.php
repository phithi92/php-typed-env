<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\SizeCaster;
use Phithi92\TypedEnv\Constraint\MaxConstraint;
use Phithi92\TypedEnv\Constraint\MinConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing sizes in bytes.
 *
 * Examples:
 *  - "512B" => 512
 *  - "10K"  => 10240
 *  - "2M"   => 2097152
 *  - "1G"   => 1073741824
 */
final class SizeKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new SizeCaster());
    }

    /**
     * Ensure the size is at least the given number of bytes.
     */
    public function minBytes(int $bytes): SizeKeyRule
    {
        return $this->addConstraint(new MinConstraint($bytes));
    }

    /**
     * Ensure the size is at most the given number of bytes.
     */
    public function maxBytes(int $bytes): SizeKeyRule
    {
        return $this->addConstraint(new MaxConstraint($bytes));
    }

    /**
     * Ensure the size is between two byte values.
     */
    public function rangeBytes(int $min, int $max): SizeKeyRule
    {
        return $this
            ->addConstraint(new MinConstraint($min))
            ->addConstraint(new MaxConstraint($max));
    }
}
