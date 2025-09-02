<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\IpCaster;
use Phithi92\TypedEnv\Constraint\IpCidrConstraint;
use Phithi92\TypedEnv\Constraint\IpDenyListConstraint;
use Phithi92\TypedEnv\Constraint\IpEnumConstraint;
use Phithi92\TypedEnv\Constraint\IpPrivateConstraint;
use Phithi92\TypedEnv\Constraint\IpVersionConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

final class IpKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new IpCaster());
    }

    public function onlyIPv4(): IpKeyRule
    {
        return $this->addConstraint(new IpVersionConstraint(4));
    }

    public function onlyIPv6(): IpKeyRule
    {
        return $this->addConstraint(new IpVersionConstraint(6));
    }

    public function allowPrivate(bool $allow = true): IpKeyRule
    {
        return $this->addConstraint(new IpPrivateConstraint($allow));
    }

    /**
     * @param list<string> $ips
     */
    public function allowList(array $ips): IpKeyRule
    {
        return $this->addConstraint(new IpEnumConstraint($ips));
    }

    /**
     * @param list<string> $ips
     */
    public function denyList(array $ips): IpKeyRule
    {
        return $this->addConstraint(new IpDenyListConstraint($ips));
    }

    public function withinCidr(string $cidr): IpKeyRule
    {
        return $this->addConstraint(new IpCidrConstraint($cidr));
    }
}
