<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use DateTimeImmutable;
use InvalidArgumentException;

final class DateTimeCasterTest extends TestCase
{
    public function testValidDateTimeImmutable(): void
    {
        $r = (new KeyRule('D'))->typeDateTime('Y-m-d', true);
        $dt = $r->apply('2024-12-31');
        self::assertInstanceOf(DateTimeImmutable::class, $dt);
        self::assertSame('2024-12-31', $dt->format('Y-m-d'));
    }

    public function testInvalidDate(): void
    {
        $r = (new KeyRule('D'))->typeDateTime('Y-m-d');
        $this->expectException(InvalidArgumentException::class);
        $r->apply('2024-02-30');
    }
}
