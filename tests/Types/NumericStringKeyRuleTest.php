<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\NumericStringKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class NumericStringKeyRuleTest extends TestCase
{
    public function testValidNumericStringLengthRange(): void
    {
        $rule = new NumericStringKeyRule('PIN');
        $rule->rangeLength(4, 6);

        $this->assertSame('1234', $rule->apply('1234'));
    }

    public function testInvalidNumericStringCast(): void
    {
        $this->expectException(CastException::class);

        (new NumericStringKeyRule('PIN'))->apply('12ab');
    }
}
