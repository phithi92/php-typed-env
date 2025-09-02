<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\UrlCaster;
use Phithi92\TypedEnv\Constraint\PatternConstraint;
use Phithi92\TypedEnv\Constraint\UrlFilterHostnamesConstraint;
use Phithi92\TypedEnv\Constraint\UrlFilterTldsConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing URLs.
 *
 * Examples:
 *  - "https://example.com"
 *  - "http://localhost:8080/api"
 */
final class UrlKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new UrlCaster());
    }

    /**
     * Restrict the URL to specific allowed schemes.
     *
     * @param list<string> $schemes Allowed schemes like ['http', 'https']
     */
    public function allowedSchemes(array $schemes): self
    {
        $pattern = sprintf('/^(%s):\/\//i', implode('|', array_map('preg_quote', $schemes)));
        return $this->addConstraint(new PatternConstraint($pattern));
    }

    /**
     * Restrict the URL to a specific host or list of hosts.
     *
     * @param list<string> $allowedHosts Allowed hostnames (exact match)
     */
    public function allowedHosts(array $allowedHosts): self
    {
        return $this->addConstraint(new UrlFilterHostnamesConstraint($allowedHosts, true));
    }

    /**
     * Restrict the URL by forbidding specific hostnames.
     *
     * @param list<string> $allowedHosts Forbidden hostnames (exact match)
     */
    public function forbiddenHosts(array $allowedHosts): self
    {
        return $this->addConstraint(new UrlFilterHostnamesConstraint($allowedHosts, false));
    }

    /**
     * Restrict the URL to specific Top-Level Domains (TLDs).
     *
     * @param list<string> $allowedTlds Allowed TLDs like ['com', 'org', 'net']
     */
    public function allowedTlds(array $allowedTlds): self
    {
        return $this->addConstraint(new UrlFilterTldsConstraint($allowedTlds, true));
    }

    /**
     * Restrict the URL by forbidding specific Top-Level Domains (TLDs).
     *
     * @param list<string> $allowedTlds Forbidden TLDs like ['xyz', 'ru', 'spam']
     */
    public function forbiddenTlds(array $allowedTlds): self
    {
        return $this->addConstraint(new UrlFilterTldsConstraint($allowedTlds, false));
    }
}
