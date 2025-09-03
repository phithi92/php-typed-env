<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Constraint\EnumConstraint;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class EnumConstraintTest extends TestCase
{
    public function testInvalidEnum(): void
    {
        $r = new EnumConstraint(['red','green','blue']);

        $this->expectException(ConstraintException::class);
        $r->assert('C', 'yellow');
    }

    public function testTypeStrictness(): void
    {
        $r = new EnumConstraint(['red','green','blue']);

        $r->assert('A', 'red');

        $this->expectException(ConstraintException::class);
        $r->assert('C', 'yellow');
    }
}
