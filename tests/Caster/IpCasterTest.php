<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class IpCasterTest extends TestCase
{
    public function testValidIpV4(): void
    {
        $r = (new KeyRule('IP'))->typeIp();
        self::assertSame('127.0.0.1', $r->apply('127.0.0.1'));
    }

    public function testValidIpV6(): void
    {
        $r = (new KeyRule('IP'))->typeIp();
        self::assertSame('::1', $r->apply('::1'));
    }

    public function testInvalidIp(): void
    {
        $r = (new KeyRule('IP'))->typeIp();
        $this->expectException(CastException::class);
        $r->apply('999.999.999.999');
    }
}
