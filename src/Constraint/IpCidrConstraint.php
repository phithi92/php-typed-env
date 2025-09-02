<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class IpCidrConstraint implements ConstraintInterface
{
    public function __construct(private string $cidr)
    {
        if (! str_contains($cidr, '/')) {
            throw new \InvalidArgumentException("Invalid CIDR notation: {$cidr}");
        }
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: expected IP string, got " . gettype($value));
        }

        [$subnet, $prefix] = explode('/', $this->cidr, 2);
        $prefix = (int) $prefix;

        // Convert both to binary for comparison
        $ipBinary = $this->ipToBinary($value);
        $subnetBinary = $this->ipToBinary($subnet);

        if (substr($ipBinary, 0, $prefix) !== substr($subnetBinary, 0, $prefix)) {
            throw new ConstraintException("ENV {$key}: {$value} is not within {$this->cidr}");
        }

        return $value;
    }

    private function ipToBinary(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return $this->ipv4ToNumericString($ip);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            return $this->ipv6ToBinaryString($ip);
        }

        throw new ConstraintException("Invalid IP address: {$ip}");
    }

    private function ipv4ToNumericString(string $ip): string
    {
        $long = ip2long($ip);

        if ($long === false) {
            throw throw new ConstraintException(
                'Failed to convert IP address to a numeric representation. Ensure it is a valid IPv4 address.'
            );
        }
        return str_pad(decbin($long), 32, '0', STR_PAD_LEFT);
    }

    private function ipv6ToBinaryString(string $ip): string
    {
        $packed = inet_pton($ip);
        if ($packed === false) {
            throw throw new ConstraintException(
                'Failed to convert IP address to a binary representation. Ensure it is a valid IPv6 address.'
            );
        }

        return implode('', array_map(
            static fn ($byte) => str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT),
            str_split($packed)
        ));
    }
}
