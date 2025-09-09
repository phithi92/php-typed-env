<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\PortCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class PortCasterTest extends TestCase
{
    private const VALID_CASES = [
        'lowest valid port'  => ['1', 1],
        'common http'        => ['80', 80],
        'https port'         => ['443', 443],
        'high valid port'    => ['65535', 65535],
    ];

    private const INVALID_CASES = [
        'zero'              => '0',          // port must be >= 1
        'too large'         => '65536',      // > max 65535
        'negative'          => '-1',
        'non-numeric'       => 'abc',
        'float number'      => '3.14',
        'empty string'      => '',
        'whitespace only'   => '   ',
    ];

    public function testValidPorts(): void
    {
        $caster = new PortCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('PORT', $input),
                "Failed asserting valid port for case: {$label}"
            );
        }
    }

    public function testInvalidPorts(): void
    {
        $caster = new PortCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('PORT', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
