<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Constraint;

use Phithi92\TypedEnv\Contracts\ConstraintInterface;
use Phithi92\TypedEnv\Exception\ConstraintException;

final class IpVersionConstraint implements ConstraintInterface
{
    public function __construct(private int $version)
    {
        if (! in_array($this->version, [4, 6], true)) {
            throw new \InvalidArgumentException('IP version must be 4 or 6.');
        }
    }

    public function assert(string $key, mixed $value): mixed
    {
        if (! is_string($value)) {
            throw new ConstraintException("ENV {$key}: expected IP string, got " . gettype($value));
        }

        $flag = $this->version === 4 ? FILTER_FLAG_IPV4 : FILTER_FLAG_IPV6;
        if (filter_var($value, FILTER_VALIDATE_IP, $flag) === false) {
            throw new ConstraintException("ENV {$key}: {$value} is not a valid IPv{$this->version} address.");
        }

        return $value;
    }
}
