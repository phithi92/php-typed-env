<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\ChmodCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class ChmodCasterTest extends TestCase
{
    public function testChmodCasterOctal(): void
    {
        $caster = new ChmodCaster();
        $this->assertSame(0o755, $caster->cast('MODE', '755'));
        $this->assertSame(0o755, $caster->cast('MODE', '0755'));
        $this->expectException(CastException::class);
        $caster->cast('MODE', '889');
    }
}
