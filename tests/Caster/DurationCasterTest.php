<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\DurationCaster;

final class DurationCasterTest extends TestCase
{
    private function assertCastedSeconds(string $mode, string $input, int $expected): void
    {
        $caster = new DurationCaster(true, $mode);
        $this->assertSame($expected, $caster->cast('T', $input)->s, "mode={$mode}, input={$input}");
    }

    public function testDurationRoundingCeil(): void
    {
        $this->assertCastedSeconds('ceil', '3450ms', 4);
    }

    public function testDurationRoundingFloor(): void
    {
        $this->assertCastedSeconds('floor', '3450ms', 3);
    }

    public function testDurationRoundingRound(): void
    {
        $this->assertCastedSeconds('round', '3450ms', 3);
    }

    // (optional) sinnvolle Grenzwerte – weiterhin nur Testcode:
    public function testDurationRoundingBoundaries(): void
    {
        $this->assertCastedSeconds('round', '3500ms', 4);
        $this->assertCastedSeconds('floor', '3499ms', 3);
        $this->assertCastedSeconds('ceil', '3000ms', 3);
        $this->assertCastedSeconds('ceil', '3001ms', 4);
    }
}
