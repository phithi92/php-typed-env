<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\IntCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class IntCasterTest extends TestCase
{
    private const VALID_CASES = [
        'positive integer'    => ['42', 42],
        'negative integer'    => ['-7', -7],
        'zero'                => ['0', 0],
        'large number'        => ['123456789', 123456789],
    ];

    private const INVALID_CASES = [
        'float value'         => '3.14',
        'alphanumeric string' => '42abc',
        'empty string'        => '',
        'whitespace only'     => '   ',
        'non-numeric'         => 'foo',
    ];

    public function testValidIntegers(): void
    {
        $caster = new IntCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('I', $input),
                "Failed asserting valid integer for case: {$label}"
            );
        }
    }

    public function testInvalidIntegers(): void
    {
        $caster = new IntCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('I', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true); // expected
            }
        }
    }
}
