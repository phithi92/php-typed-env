<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class UrlCasterTest extends TestCase
{
    public function testValidUrl(): void
    {
        $r = (new KeyRule('U'))->typeUrl();
        self::assertSame('https://example.com', $r->apply('https://example.com'));
    }

    public function testInvalidUrl(): void
    {
        $r = (new KeyRule('U'))->typeUrl();
        $this->expectException(CastException::class);
        $r->apply('not-a-url');
    }
}
