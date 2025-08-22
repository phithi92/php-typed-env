<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use InvalidArgumentException;

final class ExistsConstraint extends AbstractPathConstraint
{
    public function assert(string $key, mixed $value): mixed
    {
        $path = self::path($value);
        if (! $this->isFileOrDirectory($path)) {
            throw new InvalidArgumentException("path '{$path}' does not exist");
        }
        return $path;
    }

    private function isFileOrDirectory(string $path): bool
    {
        return file_exists($path);
    }
}
