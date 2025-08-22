<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Contracts;

/**
 * @author phillipthiele
 */
interface CasterInterface
{
    public function cast(string $key, string $raw): mixed;
}
