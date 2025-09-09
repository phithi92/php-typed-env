<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\LocaleCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class LocaleCasterTest extends TestCase
{
    public function testLocaleCasterPosixWithInvalidValue(): void
    {
        $caster = new LocaleCaster(LocaleCaster::FORMAT_POSIX);
        $this->expectException(CastException::class);
        $caster->cast('LOC', 'de-de');
    }

    public function testLocaleCasterBcp47WithInvalidValue(): void
    {
        $caster = new LocaleCaster(LocaleCaster::FORMAT_BCP47);
        $this->expectException(CastException::class);
        $caster->cast('LOC', 'de_de');
    }

    public function testLocaleCasterPosix(): void
    {
        $caster = new LocaleCaster(LocaleCaster::FORMAT_POSIX);
        $this->assertSame('de_DE', $caster->cast('LOC', 'de_DE'));
    }

    public function testLocaleCasterBcp47(): void
    {
        $caster = new LocaleCaster(LocaleCaster::FORMAT_BCP47);
        $this->assertSame('de-DE', $caster->cast('LOC', 'de-DE'));
    }
}
