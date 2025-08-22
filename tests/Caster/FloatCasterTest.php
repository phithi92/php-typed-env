<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class FloatCasterTest extends TestCase
{
    public function testValidFloats(): void
    {
        $r = (new KeyRule('F'))->typeFloat();
        self::assertSame(3.14, $r->apply('3.14'));
        self::assertSame(1000.0, $r->apply('1e3'));
    }

    public function testInvalidFloats(): void
    {
        $r = (new KeyRule('F'))->typeFloat();
        $this->expectException(CastException::class);
        $r->apply('abc');
    }
}
