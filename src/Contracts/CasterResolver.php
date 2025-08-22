<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Contracts;

interface CasterResolver
{
    /**
     * @param array<mixed> $options
     */
    public function resolve(string $type, array $options = []): CasterInterface;
}
