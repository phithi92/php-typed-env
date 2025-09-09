<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\NumericStringCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class NumericStringCasterTest extends TestCase
{
    private const VALID_CASES = [
        'integer digits'   => ['123', '123'],
        'leading zeros'    => ['00123', '00123'],
        'only zero'        => ['0', '0'],
        'negative number'  => ['-42', '-42'],
    ];

    private const INVALID_CASES = [
        'float style'        => ['3.14'],
        'scientific'         => ['1e3'],
        'letters'            => ['abc'],
        'mixed alphanumeric' => ['12a'],
        'empty string'       => [''],
        'whitespace only'    => ['   '],
        'special chars'      => ['123,45'],
    ];

    public function testValidNumericStrings(): void
    {
        $caster = new NumericStringCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('NS', $input),
                "Failed asserting valid numeric string for case: {$label}"
            );
        }
    }

    public function testInvalidNumericStrings(): void
    {
        $caster = new NumericStringCaster();

        foreach (self::INVALID_CASES as $label => [$input]) {
            try {
                $caster->cast('NS', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
