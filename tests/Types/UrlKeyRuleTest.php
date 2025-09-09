<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\UrlKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class UrlKeyRuleTest extends TestCase
{
    public function testValidUrl(): void
    {
        $rule = new UrlKeyRule('APP_URL');
        $this->assertSame('https://example.com', $rule->apply('https://example.com'));
    }

    public function testInvalidUrlCast(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("Invalid URL: not a url");

        (new UrlKeyRule('APP_URL'))->apply('not a url');
    }

    public function testAllowedSchemesAndHosts(): void
    {
        $rule = new UrlKeyRule('APP_URL');
        $rule->allowedSchemes(['https'])->allowedHosts(['example.com']);
        $this->assertSame('https://example.com', $rule->apply('https://example.com'));

        $this->expectException(ConstraintException::class);
        $rule->apply('http://example.com');

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('The URL for "APP_URL" has an invalid host');
        $rule->apply('https://evil.com');
    }
}
