<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\DateKeyRule;

final class DateKeyRuleTest extends TestCase
{
    private const DATETIME_FORMAT = 'Y-m-d';

    public function testValidDateTimeAndBetween(): void
    {
        $rule = new DateKeyRule('RUN_AT', self::DATETIME_FORMAT);

        $min = '2024-01-01';
        $max = '2026-01-01';
        $rule->between($min, $max);
        $dt = $rule->apply('2025-06-01');

        $this->assertSame('2025-06-01', $dt);
    }
}
