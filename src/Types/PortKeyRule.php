<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\PortCaster;
use Phithi92\TypedEnv\Constraint\MaxConstraint;
use Phithi92\TypedEnv\Constraint\MinConstraint;
use Phithi92\TypedEnv\Exception\ConstraintException;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing TCP/UDP port numbers.
 *
 * Examples:
 *  - "80"
 *  - "443"
 *  - "8080"
 */
final class PortKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new PortCaster());
    }

    /**
     * Ensure the port number is above or equal to the given minimum.
     *
     * Example: ->min(1024) to avoid privileged ports.
     */
    public function min(int $port): PortKeyRule
    {
        if ($port < 0 || $port > 65535) {
            throw new ConstraintException('Minimum port value must be between 0 and 65535.');
        }

        return $this->addConstraint(new MinConstraint($port));
    }

    /**
     * Ensure the port number is below or equal to the given maximum.
     *
     * Example: ->max(49151) to restrict to non-ephemeral ports.
     */
    public function max(int $port): PortKeyRule
    {
        if ($port < 0 || $port > 65535) {
            throw new ConstraintException('Maximum port value must be between 0 and 65535.');
        }

        return $this->addConstraint(new MaxConstraint($port));
    }

    /**
     * Restrict the port to a specific range.
     */
    public function range(int $min, int $max): PortKeyRule
    {
        return $this->min($min)->max($max);
    }
}
