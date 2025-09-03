<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\IpKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class IpKeyRuleTest extends TestCase
{
    public function testOnlyIPv4AcceptsV4AndRejectsV6(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->onlyIPv4();

        $this->assertSame('203.0.113.10', $rule->apply('203.0.113.10')); // gültige IPv4 (TEST-NET-3)
        $this->expectException(ConstraintException::class);
        $rule->apply('2001:db8::1'); // gültige IPv6, aber onlyIPv4 -> verboten
    }

    public function testOnlyIPv6AcceptsV6AndRejectsV4(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->onlyIPv6();

        $this->assertSame('2001:db8::1', $rule->apply('2001:db8::1')); // gültige IPv6 (Dokunet)
        $this->expectException(ConstraintException::class);
        $rule->apply('198.51.100.23'); // gültige IPv4 (TEST-NET-2), aber onlyIPv6 -> verboten
    }

    public function testAllowPrivateTrueAllowsPrivateAndPublic(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->allowPrivate(true);

        // Private IPv4
        $this->assertSame('192.168.1.10', $rule->apply('192.168.1.10'));
        // Öffentliche IPv4
        $this->assertSame('8.8.8.8', $rule->apply('8.8.8.8'));
        // Private IPv6 (fc00::/7)
        $this->assertSame('fc00::1', $rule->apply('fc00::1'));
    }

    public function testDisallowPrivateRejectsPrivateAllowsPublic(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->allowPrivate(false);

        // Öffentliche IPv4 erlaubt
        $this->assertSame('203.0.113.45', $rule->apply('203.0.113.45'));
        // Private IPv4 verboten
        $this->expectException(ConstraintException::class);
        $rule->apply('10.0.0.5');
    }

    public function testAllowListAllowsOnlySpecified(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->allowList(['203.0.113.10', '2001:db8::1']);

        $this->assertSame('203.0.113.10', $rule->apply('203.0.113.10'));
        $this->assertSame('2001:db8::1', $rule->apply('2001:db8::1'));

        $this->expectException(ConstraintException::class);
        $rule->apply('203.0.113.11');
    }

    public function testDenyListRejectsListedAllowsOthers(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->denyList(['203.0.113.66', '2001:db8::dead:beef']);

        // Nicht gelistete Adresse ist erlaubt
        $this->assertSame('203.0.113.67', $rule->apply('203.0.113.67'));
        // Gelistete IPv4 verboten
        $this->expectException(ConstraintException::class);
        $rule->apply('203.0.113.66');
    }

    public function testWithinCidrAcceptsInsideRejectsOutside(): void
    {
        $rule = (new IpKeyRule('APP_IP'))->withinCidr('192.168.0.0/24');

        $this->assertSame('192.168.0.5', $rule->apply('192.168.0.5')); // innerhalb /24
        $this->expectException(ConstraintException::class);
        $rule->apply('192.168.1.5'); // außerhalb /24
    }

    public function testInvalidIpCastThrowsCastException(): void
    {
        $this->expectException(CastException::class);
        (new IpKeyRule('APP_IP'))->apply('999.999.999.999'); // kein valides IP-Format
    }
}
