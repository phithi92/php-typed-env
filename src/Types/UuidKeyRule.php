<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\UuidAnyCaster;
use Phithi92\TypedEnv\Schema\KeyRule;

class UuidKeyRule extends KeyRule
{
    public function __construct(string $key, bool $uuidv4)
    {
        parent::__construct($key, new UuidAnyCaster($uuidv4));
    }
}
