<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Exception\ConstraintException;
use Phithi92\TypedEnv\Constraint\MaxConstraint;

final class MaxConstraintTest extends TestCase
{
    public function testPassesWhenBelowMax(): void
    {
        $r = new MaxConstraint(10);

        $this->assertSame(9, $r->assert('C', 9));
    }

    public function testFailsWhenAboveMax(): void
    {
        $r = new MaxConstraint(10);

        $this->expectException(ConstraintException::class);

        $this->assertSame(11, $r->assert('C', 11));
    }
}
