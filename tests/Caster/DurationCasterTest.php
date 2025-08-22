<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use DateInterval;
use Phithi92\TypedEnv\Exception\CastException;
use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\DurationCaster;

final class DurationCasterTest extends TestCase
{
    public function testValidDurationsWithDefaultFloor(): void
    {
        $caster = new DurationCaster();

        // ms rounding down
        $this->assertSame(0, $caster->cast('KEY', '500ms'));
        $this->assertSame(1, $caster->cast('KEY', '1500ms'));

        // seconds
        $this->assertSame(30, $caster->cast('KEY', '30s'));

        // minutes
        $this->assertSame(60, $caster->cast('KEY', '1m'));
        $this->assertSame(120, $caster->cast('KEY', '2m'));

        // hours
        $this->assertSame(3600, $caster->cast('KEY', '1h'));
        $this->assertSame(7200, $caster->cast('KEY', '2h'));

        // days
        $this->assertSame(86400, $caster->cast('KEY', '1d'));
    }

    public function testRoundingModes(): void
    {
        // round mode
        $casterRound = new DurationCaster(false, 'round');
        $this->assertSame(1, $casterRound->cast('KEY', '500ms')); // rounds up

        // ceil mode
        $casterCeil = new DurationCaster(false, 'ceil');
        $this->assertSame(1, $casterCeil->cast('KEY', '500ms'));

        // floor mode
        $casterFloor = new DurationCaster(false, 'floor');
        $this->assertSame(0, $casterFloor->cast('KEY', '500ms'));
    }

    public function testDurationsReturnDateInterval(): void
    {
        $caster = new DurationCaster(true); // returnInterval = true
        $interval = $caster->cast('TIMEOUT', '120s');

        $this->assertInstanceOf(DateInterval::class, $interval);
        $this->assertSame(
            120,
            $interval->s + ($interval->i * 60) + ($interval->h * 3600)
        );
    }

    public function testInvalidDurations(): void
    {
        $caster = new DurationCaster();

        // invalid format
        $this->expectException(CastException::class);
        $caster->cast('KEY', 'not-a-duration');
    }

    public function testInvalidUnit(): void
    {
        $caster = new DurationCaster();

        // invalid unit
        $this->expectException(CastException::class);
        $caster->cast('KEY', '10w');
    }

    public function testInvalidRoundingMode(): void
    {
        $this->expectException(CastException::class);
        new DurationCaster(false, 'invalid-mode');
    }
}
