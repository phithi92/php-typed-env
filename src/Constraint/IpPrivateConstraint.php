<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class IpPrivateConstraint implements ConstraintInterface
{
    public function __construct(private bool $allowPrivate)
    {
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: expected IP string, got " . gettype($value));
        }

        $isPrivate = $this->isPrivateIp($value);

        if (! $this->allowPrivate && $isPrivate) {
            throw new ConstraintException("ENV {$key}: {$value} is private, but only public IPs are allowed.");
        }

        if ($this->allowPrivate && ! $isPrivate) {
            // nothing special to enforce
            return $value;
        }

        return $value;
    }

    private function isPrivateIp(string $ip): bool
    {
        // IPv4 private ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return preg_match(
                '/^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/',
                $ip
            ) === 1;
        }

        // IPv6 private range (fc00::/7)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            return str_starts_with(strtolower($ip), 'fc') || str_starts_with(strtolower($ip), 'fd');
        }

        return false;
    }
}
