<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\VersionCaster;
use Phithi92\TypedEnv\Constraint\VersionConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing version strings.
 *
 * Supports semantic versioning:
 *  - "1.0.0"
 *  - "v2.3.4"
 *  - "1.0.0-beta"
 *  - "1.2.3+build.123"
 */
final class VersionKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new VersionCaster());
    }

    /**
     * Ensure the version is at least the given version.
     */
    public function minVersion(string $min): static
    {
        return $this->addConstraint(new VersionConstraint('>=', $min));
    }

    /**
     * Ensure the version is at most the given version.
     */
    public function maxVersion(string $max): static
    {
        return $this->addConstraint(new VersionConstraint('<=', $max));
    }

    /**
     * Ensure the version is between two versions (inclusive).
     */
    public function rangeVersion(string $min, string $max): static
    {
        return $this
            ->addConstraint(new VersionConstraint('>=', $min))
            ->addConstraint(new VersionConstraint('<=', $max));
    }
}
