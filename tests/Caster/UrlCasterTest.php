<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\UrlCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class UrlCasterTest extends TestCase
{
    private const VALID_CASES = [
        'https url'      => ['https://example.com', 'https://example.com'],
        'http url'       => ['http://example.org', 'http://example.org'],
        'ftp url'        => ['ftp://ftp.example.net', 'ftp://ftp.example.net'],
        'with path'      => ['https://example.com/foo/bar', 'https://example.com/foo/bar'],
        'with query'     => ['https://example.com/?a=1&b=2', 'https://example.com/?a=1&b=2'],
        'with port'      => ['https://example.com:8080', 'https://example.com:8080'],
    ];

    private const INVALID_CASES = [
        'unsupported scheme' => 'htp://bad',
        'missing host'       => 'http://',
        'no scheme'          => 'example.com',
        'empty string'       => '',
        'whitespace only'    => '   ',
    ];

    public function testValidUrls(): void
    {
        $caster = new UrlCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('U', $input),
                "Failed asserting valid URL for case: {$label}"
            );
        }
    }

    public function testInvalidUrls(): void
    {
        $caster = new UrlCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('U', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true, "Caught expected exception for case: {$label}");
            }
        }
    }
}
