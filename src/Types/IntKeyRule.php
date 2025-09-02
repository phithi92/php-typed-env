<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\IntCaster;
use Phithi92\TypedEnv\Constraint;
use Phithi92\TypedEnv\Schema\KeyRule;

final class IntKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new IntCaster());
    }

    public function min(int $value): IntKeyRule
    {
        return $this->addConstraint(new Constraint\MinConstraint($value));
    }

    public function max(int $value): IntKeyRule
    {
        return $this->addConstraint(new Constraint\MaxConstraint($value));
    }
}
