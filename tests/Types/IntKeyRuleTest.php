<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\IntKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class IntKeyRuleTest extends TestCase
{
    public function testCastsAndAppliesMinMax(): void
    {
        $rule = new IntKeyRule('INT_KEY');
        $rule->min(1)->max(10);

        $this->assertSame(5, $rule->apply('5'));
    }

    public function testInvalidIntThrowsCastExceptionWithMessage(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV INT_KEY: 'abc' is not a valid int");

        (new IntKeyRule('INT_KEY'))->apply('abc');
    }

    public function testViolatesMinThrowsConstraintExceptionWithMessage(): void
    {
        $rule = new IntKeyRule('INT_KEY');
        $rule->min(3);

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('ENV INT_KEY: value 2 < min 3');
        $rule->apply('2');
    }
}
