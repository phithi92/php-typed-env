<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Caster;

use InvalidArgumentException;
use Phithi92\TypedEnv\Contracts\CasterInterface;

final class PathCaster implements CasterInterface
{
    /**
     * @param bool $resolveRealpath Resolve to absolute canonical path using realpath()
     */
    public function __construct(
        private bool $resolveRealpath = false
    ) {
    }

    /**
     * Casts the raw string into a filesystem path.
     *
     * @param string $raw The raw environment variable value
     * @param string $key The environment key (used for exception messages)
     *
     * @return string The validated path string
     *
     * @throws InvalidArgumentException If the path is empty, or resolving fails
     */
    public function cast(string $key, string $raw): string
    {
        $path = trim($raw);

        if ($path === '') {
            throw new InvalidArgumentException("Environment variable '{$key}' must not be empty for PathCaster.");
        }

        if ($this->resolveRealpath) {
            $resolved = realpath($path);
            if ($resolved === false) {
                throw new InvalidArgumentException(
                    "Environment variable '{$key}' points to a path that cannot be resolved: '{$path}'."
                );
            }
            return $resolved;
        }

        return $path;
    }
}
