<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Exception\ConstraintException;
use Phithi92\TypedEnv\Constraint\MinConstraint;

final class MinConstraintTest extends TestCase
{
    public function testPassesWhenAboveMin(): void
    {
        $r = new MinConstraint(10);

        $this->assertSame(11, $r->assert('C', 11));

        $r = new MinConstraint(10.0);

        $this->assertSame(11, $r->assert('C', 11));

        $r = new MinConstraint(10.0);

        $this->assertSame(11.0, $r->assert('C', 11.0));
    }

    public function testFailsWhenBelowMin(): void
    {
        $r = new MinConstraint(10);

        $this->expectException(ConstraintException::class);

        $this->assertSame(9, $r->assert('C', 9));

        $r = new MinConstraint(9.0);

        $this->expectException(ConstraintException::class);

        $this->assertSame(9.0, $r->assert('C', 9));

        $r = new MinConstraint(9.0);

        $this->expectException(ConstraintException::class);

        $this->assertSame(9.0, $r->assert('C', 9.0));
    }
}
