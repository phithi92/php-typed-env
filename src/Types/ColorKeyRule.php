<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\ColorCaster;
use Phithi92\TypedEnv\Schema\KeyRule;

final class ColorKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new ColorCaster());
    }
}
