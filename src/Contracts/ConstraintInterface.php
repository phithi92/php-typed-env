<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Contracts;

interface ConstraintInterface
{
    /** @return mixed (may transform or just validate) */
    public function assert(string $key, mixed $value): mixed;
}
