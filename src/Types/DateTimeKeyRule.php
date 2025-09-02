<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use DateTimeInterface;
use Phithi92\TypedEnv\Caster\DateTimeCaster;
use Phithi92\TypedEnv\Constraint\MaxDateConstraint;
use Phithi92\TypedEnv\Constraint\MinDateConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing date/time values.
 *
 * Examples:
 *  - "2024-12-31 23:59:59"
 *  - "2024-12-31T23:59:59+00:00"
 */
final class DateTimeKeyRule extends KeyRule
{
    public function __construct(string $key, string $format = DateTimeInterface::ATOM, bool $immutable = true)
    {
        parent::__construct($key, new DateTimeCaster($format, $immutable));
    }

    /**
     * Ensure the date/time is after the given point in time.
     */
    public function after(string|DateTimeInterface $min): DateTimeKeyRule
    {
        return $this->addConstraint(new MinDateConstraint($min));
    }

    /**
     * Ensure the date/time is before the given point in time.
     */
    public function before(string|DateTimeInterface $max): DateTimeKeyRule
    {
        return $this->addConstraint(new MaxDateConstraint($max));
    }

    /**
     * Ensure the date/time is between two points in time (inclusive).
     */
    public function between(string|DateTimeInterface $min, string|DateTimeInterface $max): DateTimeKeyRule
    {
        return $this->after($min)->before($max);
    }
}
