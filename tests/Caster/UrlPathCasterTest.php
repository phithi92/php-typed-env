<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\UrlPathCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class UrlPathCasterTest extends TestCase
{
    private const VALID_CASES = [
        'root path'      => ['/', '/'],
        'simple path'    => ['/api/v1', '/api/v1'],
        'with trailing'  => ['/api/v1/', '/api/v1/'],
        'deep path'      => ['/foo/bar/baz', '/foo/bar/baz'],
        'with dashes'    => ['/foo-bar', '/foo-bar'],
        'with underscore' => ['/foo_bar', '/foo_bar'],
    ];

    private const INVALID_CASES = [
        'missing leading slash' => 'api/v1',
        'double leading slash'  => '//api',
        'empty string'          => '',
        'whitespace only'       => '   ',
        'absolute url'          => 'https://example.com/api',
    ];

    public function testValidPaths(): void
    {
        $caster = new UrlPathCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('UP', $input),
                "Failed asserting valid URL path for case: {$label}"
            );
        }
    }

    public function testInvalidPaths(): void
    {
        $caster = new UrlPathCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('UP', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
