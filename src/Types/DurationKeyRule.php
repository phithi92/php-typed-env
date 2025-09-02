<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\DurationCaster;
use Phithi92\TypedEnv\Constraint\MaxConstraint;
use Phithi92\TypedEnv\Constraint\MinConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing durations (DateInterval).
 *
 * Examples of valid values:
 *  - "30s"   => 30 seconds
 *  - "5m"    => 5 minutes
 *  - "2h"    => 2 hours
 *  - "1d"    => 1 day
 *  - "PT15M" => ISO8601 duration (15 minutes)
 */
final class DurationKeyRule extends KeyRule
{
    public function __construct(string $key, bool $returnInterval = false, string $roundingMode = 'floor')
    {
        parent::__construct($key, new DurationCaster(
            returnInterval: $returnInterval,
            roundingMode: $roundingMode
        ));
    }

    /**
     * Ensure the duration is at least the given number of seconds.
     */
    public function minSeconds(int $seconds): DurationKeyRule
    {
        return $this->addConstraint(new MinConstraint($seconds));
    }

    /**
     * Ensure the duration is at most the given number of seconds.
     */
    public function maxSeconds(int $seconds): DurationKeyRule
    {
        return $this->addConstraint(new MaxConstraint($seconds));
    }

    /**
     * Ensure the duration is between the given bounds (seconds).
     */
    public function rangeSeconds(int $min, int $max): DurationKeyRule
    {
        return $this
            ->addConstraint(new MinConstraint($min))
            ->addConstraint(new MaxConstraint($max));
    }
}
