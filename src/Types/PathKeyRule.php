<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\PathCaster;
use Phithi92\TypedEnv\Constraint\ExistsConstraint;
use Phithi92\TypedEnv\Constraint\IsReadableConstraint;
use Phithi92\TypedEnv\Constraint\IsWritableConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing filesystem paths.
 *
 * Examples of valid values:
 *  - "/var/www/html"
 *  - "./storage/logs"
 *  - "C:\\Projects\\app"
 */
final class PathKeyRule extends KeyRule
{
    public function __construct(string $key, bool $resolveRealpath)
    {
        parent::__construct($key, new PathCaster($resolveRealpath));
    }

    /**
     * Ensure the path exists.
     */
    public function mustExist(): PathKeyRule
    {
        return $this->addConstraint(new ExistsConstraint());
    }

    /**
     * Ensure the path is readable.
     */
    public function isReadable(): PathKeyRule
    {
        return $this->addConstraint(new IsReadableConstraint());
    }

    /**
     * Ensure the path is writable.
     */
    public function isWritable(): PathKeyRule
    {
        return $this->addConstraint(new IsWritableConstraint());
    }

    /**
     * Ensure the path both exists and is writable.
     */
    public function existsAndWritable(): PathKeyRule
    {
        return $this
            ->addConstraint(new ExistsConstraint())
            ->addConstraint(new IsWritableConstraint());
    }
}
