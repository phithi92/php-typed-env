<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\SizeKeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class SizeKeyRuleTest extends TestCase
{
    public function testValidSizeBytesRange(): void
    {
        $rule = new SizeKeyRule('MAX_UPLOAD');
        $rule->rangeBytes(1024, 10 * 1024 * 1024);

        $this->assertSame(2048, $rule->apply('2KB'));
    }

    public function testInvalidSizeCast(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV MAX_UPLOAD: 'a lot' is not a valid size");

        (new SizeKeyRule('MAX_UPLOAD'))->apply('a lot');
    }
}
