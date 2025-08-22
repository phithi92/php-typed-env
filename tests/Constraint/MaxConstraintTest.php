<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use InvalidArgumentException;

final class MaxConstraintTest extends TestCase
{
    public function testPassesWhenBelowMax(): void
    {
        $r = (new KeyRule('N'))->typeInt()->max(100);
        self::assertSame(42, $r->apply('42'));
    }

    public function testFailsWhenAboveMax(): void
    {
        $r = (new KeyRule('N'))->typeInt()->max(10);
        $this->expectException(InvalidArgumentException::class);
        $r->apply('20');
    }
}
