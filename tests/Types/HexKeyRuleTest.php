<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\HexKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class HexKeyRuleTest extends TestCase
{
    public function testValidHexLengthRange(): void
    {
        $rule = new HexKeyRule('COLOR');
        $rule->rangeLength(3, 6);

        $this->assertSame('fff', $rule->apply('fff'));
    }

    public function testInvalidHexCast(): void
    {
        $this->expectException(CastException::class);

        (new HexKeyRule('COLOR'))->apply('gzz');
    }
}
