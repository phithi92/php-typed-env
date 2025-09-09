<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\DateCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class DateCasterTest extends TestCase
{
    public function testDateCasterParsesFormat(): void
    {
        $caster = new DateCaster('Y-m-d');
        $dt = $caster->cast('D', '2024-01-31');
        $this->assertEquals('2024-01-31', $dt);
        $this->expectException(CastException::class);
        $caster->cast('D', '31-01-2024');
    }
}
