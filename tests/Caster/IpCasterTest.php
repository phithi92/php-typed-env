<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\IpCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class IpCasterTest extends TestCase
{
    private const VALID_CASES = [
        'ipv4 loopback'   => ['127.0.0.1', '127.0.0.1'],
        'ipv4 private'    => ['192.168.0.1', '192.168.0.1'],
        'ipv4 broadcast'  => ['255.255.255.255', '255.255.255.255'],
        'ipv6 loopback'   => ['::1', '::1'],
        'ipv6 full form'  => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
        'ipv6 shortened'  => ['2001:db8::1', '2001:db8::1'],
    ];

    private const INVALID_CASES = [
        'invalid ipv4 too large' => '999.0.0.1',
        'invalid ipv4 segment'   => '192.168.0.256',
        'ipv4 too few segments'  => '192.168.0',
        'non-numeric ipv4'       => 'abc.def.ghi.jkl',
        'invalid ipv6'           => '2001:::7334',
        'empty string'           => '',
        'whitespace only'        => '   ',
    ];

    public function testValidIpAddresses(): void
    {
        $caster = new IpCaster();

        foreach (self::VALID_CASES as $label => [$input, $expected]) {
            $this->assertSame(
                $expected,
                $caster->cast('IP', $input),
                "Failed asserting valid IP for case: {$label}"
            );
        }
    }

    public function testInvalidIpAddresses(): void
    {
        $caster = new IpCaster();

        foreach (self::INVALID_CASES as $label => $input) {
            try {
                $caster->cast('IP', $input);
                $this->fail("Expected CastException for case: {$label}");
            } catch (CastException $e) {
                $this->assertTrue(true); // expected
            }
        }
    }
}
