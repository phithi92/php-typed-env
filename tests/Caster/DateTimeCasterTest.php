<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use DateTimeInterface;
use DateTimeImmutable;
use DateTime;
use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\DateTimeCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class DateTimeCasterTest extends TestCase
{
    public function testDateTimeCasterReturnsDateTime()
    {
        $caster = new DateTimeCaster(DateTimeInterface::ATOM, true);
        $dt = $caster->cast('DT', '2024-01-31T10:00:00+00:00');
        $this->assertInstanceOf(DateTimeImmutable::class, $dt);
    }

    public function testDateTimeCasterReturnsDateTimeImmutable()
    {
        $caster = new DateTimeCaster(DateTimeInterface::ATOM, false);
        $dt = $caster->cast('DT', '2024-01-31T10:00:00+00:00');
        $this->assertInstanceOf(DateTime::class, $dt);
    }

    public function testDateTimeCasterWithInvalidValue(): void
    {
        $caster = new DateTimeCaster(DateTimeInterface::ATOM, true);
        $this->expectException(CastException::class);
        $caster->cast('DT', 'bad');
    }
}
