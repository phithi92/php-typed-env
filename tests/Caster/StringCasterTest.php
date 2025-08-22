<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;

final class StringCasterTest extends TestCase
{
    public function testString(): void
    {
        $rule = (new KeyRule('S'))->typeString();
        self::assertSame('hello', $rule->apply('hello'));
    }

    public function testFallbackToStringCaster(): void
    {
        // no type set → should default to string
        $rule = new KeyRule('S');
        self::assertSame('42', $rule->apply('42'));
    }
}
