<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\DateCaster;
use Phithi92\TypedEnv\Constraint\MaxDateConstraint;
use Phithi92\TypedEnv\Constraint\MinDateConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing dates or datetimes.
 *
 * Examples of valid values:
 *  - "2025-08-22"
 *  - "2025-08-22 14:30:00"
 *  - "2025-08-22T14:30:00Z"
 */
final class DateKeyRule extends KeyRule
{
    public function __construct(string $key, string $format)
    {
        parent::__construct($key, new DateCaster($format));
    }

    /**
     * Ensure the date is not earlier than the given.
     */
    public function minDate(string $date): static
    {
        return $this->addConstraint(new MinDateConstraint($date));
    }

    /**
     * Ensure the date is not later than the given.
     */
    public function maxDate(string $date): static
    {
        return $this->addConstraint(new MaxDateConstraint($date));
    }

    /**
     * Ensure the date is between two given bounds.
     */
    public function between(string $min, string $max): static
    {
        return $this
            ->addConstraint(new MinDateConstraint($min))
            ->addConstraint(new MaxDateConstraint($max));
    }
}
