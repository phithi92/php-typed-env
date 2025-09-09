<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\BoolCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class BoolCasterTest extends TestCase
{
    public function testBoolCasterBasicVariants(): void
    {
        $caster = new BoolCaster();
        $this->assertTrue($caster->cast('B', '1'));
        $this->assertFalse($caster->cast('B', '0'));
        $this->assertTrue($caster->cast('B', 'true'));
        $this->assertFalse($caster->cast('B', 'false'));
        $this->expectException(CastException::class);
        $caster->cast('B', 'maybe');
    }
}
