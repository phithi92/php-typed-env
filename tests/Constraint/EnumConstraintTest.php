<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class EnumConstraintTest extends TestCase
{
    public function testValidEnum(): void
    {
        $r = (new KeyRule('C'))->typeString()->enum(['red','green','blue']);
        self::assertSame('green', $r->apply('green'));
    }

    public function testInvalidEnum(): void
    {
        $r = (new KeyRule('C'))->typeString()->enum(['red','green','blue']);
        $this->expectException(ConstraintException::class);
        $r->apply('yellow');
    }

    public function testTypeStrictness(): void
    {
        $r = (new KeyRule('C'))->typeInt()->enum([1,2,3]);
        self::assertSame(2, $r->apply('2'));

        $this->expectException(ConstraintException::class);
        $r->apply('4');
    }
}
