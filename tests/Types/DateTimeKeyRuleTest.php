<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\DateTimeKeyRule;
use DateTimeImmutable;
use DateTimeInterface;

final class DateTimeKeyRuleTest extends TestCase
{
    private const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function testValidDateTimeAndBetween(): void
    {
        $rule = new DateTimeKeyRule('RUN_AT', self::DATETIME_FORMAT);

        $min = DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, '2024-01-01 00:00:00');
        $max = DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, '2026-01-01 00:00:00');
        $rule->between($min, $max);
        $dt = $rule->apply('2025-06-01 00:00:00');

        $this->assertInstanceOf(DateTimeInterface::class, $dt);
        $this->assertSame('2025-06-01 00:00:00', $dt->format(self::DATETIME_FORMAT));
    }
}
