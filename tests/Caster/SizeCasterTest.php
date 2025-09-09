<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\SizeCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class SizeCasterTest extends TestCase
{
    private const VALID_CASES = [
        'bytes no unit'   => ['100', 100],
        'kilobytes'       => ['10KB', 10 * 1024],
        'kilobytes lower' => ['10kb', 10 * 1024],
        'megabytes'       => ['2MB', 2 * 1024 * 1024],
        'gigabytes'       => ['1GB', 1024 * 1024 * 1024],
        'with spaces'     => [' 5 MB ', 5 * 1024 * 1024],
    ];

    private const INVALID_CASES = [
        'letters only'    => 'abc',
        'wrong unit'      => '10XB',
        'negative'        => '-1KB',
        'float number'    => '1.5MB',   // falls dein Caster nur Ganzzahlen erlaubt
        'empty string'    => '',
        'whitespace only' => '   ',
    ];

    public function testValidSizes(): void
    {
        $caster = new SizeCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('SZ', $input),
                "Failed asserting valid size for case: {$label}"
            );
        }
    }

    public function testInvalidSizes(): void
    {
        $caster = new SizeCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('SZ', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
