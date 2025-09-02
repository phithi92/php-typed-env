<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\BoolCaster;
use Phithi92\TypedEnv\Schema\KeyRule;

final class BoolKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new BoolCaster());
    }
}
