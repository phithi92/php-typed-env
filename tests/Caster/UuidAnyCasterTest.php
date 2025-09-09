<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\UuidAnyCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class UuidAnyCasterTest extends TestCase
{
    private const VALID_CASES = [
        // v1: version nibble ist "1"
        'valid v1' => ['123e4567-e89b-11d3-a456-426614174000', false],
        // v4: version nibble ist "4"
        'valid v4' => ['123e4567-e89b-42d3-a456-426614174000', true],
    ];

    private const INVALID_CASES = [
        'wrong format'  => 'not-a-uuid',
        'missing parts' => '123e4567-e89b-42d3-a456',
        'too short'     => '123e4567-e89b-42d3-a456-42661417400',
        'too long'      => '123e4567-e89b-42d3-a456-426614174000abcd',
        'bad hex chars' => '123e4567-e89b-42d3-a456-42661417400Z',
    ];

    public function testValidUuids(): void
    {

        foreach (self::VALID_CASES as $label => [$input,$uuidv4]) {
            $caster = new UuidAnyCaster($uuidv4);

            $this->assertSame(
                strtolower($input),
                $caster->cast('ID', $input),
                "Failed asserting valid UUID for case: {$label}"
            );
        }
    }

    public function testInvalidUuids(): void
    {
        $caster = new UuidAnyCaster(true);

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('ID', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
