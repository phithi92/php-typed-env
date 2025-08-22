<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use InvalidArgumentException;

final class MinConstraintTest extends TestCase
{
    public function testPassesWhenAboveMin(): void
    {
        $r = (new KeyRule('N'))->typeInt()->min(10);
        self::assertSame(42, $r->apply('42'));
    }

    public function testFailsWhenBelowMin(): void
    {
        $r = (new KeyRule('N'))->typeInt()->min(10);
        $this->expectException(InvalidArgumentException::class);
        $r->apply('5');
    }
}
