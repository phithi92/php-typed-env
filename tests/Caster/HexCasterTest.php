<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\HexCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class HexCasterTest extends TestCase
{
    private const VALID_CASES = [
        'uppercase letters'   => ['DEADBEEF', 'deadbeef'],
        'lowercase letters'   => ['deadbeef', 'deadbeef'],
        'mixed case'          => ['DeAdBeEf', 'deadbeef'],
        'numbers only'        => ['01234567', '01234567'],
    ];

    private const INVALID_CASES = [
        'non-hex characters'  => 'zzzzzzzz',
        'too short'           => 'abc',
        'too long'            => 'deadbeef42',
        'empty string'        => '',
        'whitespace'          => 'dead beef',
    ];

    public function testValidHexValues(): void
    {
        $caster = new HexCaster(8);

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('H', $input),
                "Failed asserting valid hex for case: {$label}"
            );
        }
    }

    public function testInvalidHexValues(): void
    {
        $caster = new HexCaster(8);

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('H', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true); // expected
            }
        }
    }
}
