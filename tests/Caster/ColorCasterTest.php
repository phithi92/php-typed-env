<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\ColorCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class ColorCasterTest extends TestCase
{
    public function testColorCasterAcceptsHexAndRgb(): void
    {
        $caster = new ColorCaster();
        $this->assertSame('#ff0000', $caster->cast('C', '#ff0000'));
        $this->assertSame('rgb(255, 0, 0)', $caster->cast('C', 'rgb(255, 0, 0)'));
        $this->assertSame('rgba(255, 0, 0, .1)', $caster->cast('C', 'rgba(255, 0, 0, .1)'));
        $this->expectException(CastException::class);
        $caster->cast('C', 'not-a-color');
    }
}
