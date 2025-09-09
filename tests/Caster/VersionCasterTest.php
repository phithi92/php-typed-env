<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\VersionCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class VersionCasterTest extends TestCase
{
    private const VALID_CASES = [
        'basic semver'       => ['1.2.3', '1.2.3'],
        'with pre-release'   => ['1.2.3-alpha', '1.2.3-alpha'],
        'with build meta'    => ['1.2.3+build.5', '1.2.3+build.5'],
        'with both'          => ['1.2.3-beta+exp.sha.5114f85', '1.2.3-beta+exp.sha.5114f85'],
        'large numbers'      => ['123.456.789', '123.456.789'],
        'zero components'    => ['0.0.1', '0.0.1'],
    ];

    private const INVALID_CASES = [
        'missing patch'      => '1.2',
        'missing minor'      => '1',
        'non-numeric major'  => 'a.1.2',
        'non-numeric minor'  => '1.b.2',
        'non-numeric patch'  => '1.2.c',
        'empty string'       => '',
        'whitespace only'    => '   ',
        'extra dot'          => '1.2.3.4',
    ];

    public function testValidVersions(): void
    {
        $caster = new VersionCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('V', $input),
                "Failed asserting valid version for case: {$label}"
            );
        }
    }

    public function testInvalidVersions(): void
    {
        $caster = new VersionCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('V', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
