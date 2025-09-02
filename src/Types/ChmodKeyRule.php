<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\ChmodCaster;
use Phithi92\TypedEnv\Constraint\MaxConstraint;
use Phithi92\TypedEnv\Constraint\MinConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing chmod permissions.
 *
 * Examples of valid values:
 *  - "0644"  => typical read/write for owner, read for group/others
 *  - "0755"  => typical directory permissions
 *  - "0700"  => private access
 */
final class ChmodKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new ChmodCaster());
    }

    /**
     * Ensure the chmod value is at least the given octal number.
     */
    public function min(int $octal): ChmodKeyRule
    {
        return $this->addConstraint(new MinConstraint($octal));
    }

    /**
     * Ensure the chmod value is at most the given octal number.
     */
    public function max(int $octal): ChmodKeyRule
    {
        return $this->addConstraint(new MaxConstraint($octal));
    }

    /**
     * Ensure the chmod value is within a specific range.
     */
    public function range(int $min, int $max): ChmodKeyRule
    {
        return $this
            ->addConstraint(new MinConstraint($min))
            ->addConstraint(new MaxConstraint($max));
    }
}
