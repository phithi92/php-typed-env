<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\FloatCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class FloatCasterTest extends TestCase
{
    private const VALID_CASES = [
        'simple float'       => ['3.14', 3.14],
        'integer string'     => ['42', 42.0],
        'negative float'     => ['-1.5', -1.5],
        'scientific notation' => ['1.2e3', 1.2e3],
        'zero int'           => ['0', 0.0],
        'zero float'         => ['0.0', 0.0],
    ];

    private const INVALID_CASES = [
        'empty string'        => '',
        'non-numeric'         => 'abc',
        'float with suffix'   => '3.14abc',
        'whitespace only'     => '   ',
    ];

    public function testValidFloats(): void
    {
        $caster = new FloatCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('F', $input),
                "Failed asserting valid float for case: {$label}"
            );
        }
    }

    public function testInvalidFloats(): void
    {
        $caster = new FloatCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('F', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true); // exception expected
            }
        }
    }
}
