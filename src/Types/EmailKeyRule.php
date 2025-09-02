<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Types;

use Phithi92\TypedEnv\Caster\EmailCaster;
use Phithi92\TypedEnv\Constraint\EmailFilterDomainsConstraint;
use Phithi92\TypedEnv\Constraint\EmailFilterTldsConstraint;
use Phithi92\TypedEnv\Schema\KeyRule;

/**
 * Rule for environment variables representing email addresses.
 *
 * Examples:
 *  - "admin@example.com"
 *  - "support@mydomain.org"
 */
final class EmailKeyRule extends KeyRule
{
    public function __construct(string $key)
    {
        parent::__construct($key, new EmailCaster());
    }

    /**
     * Restrict the email address to specific allowed domains.
     *
     * @param list<string> $allowedDomains Allowed domains like ['example.com', 'mydomain.org']
     */
    public function allowedDomains(array $allowedDomains): self
    {
        return $this->addConstraint(new EmailFilterDomainsConstraint($allowedDomains, true));
    }

    /**
     * Restrict the email address by forbidding specific domains.
     *
     * @param list<string> $allowedDomains Forbidden domains like ['spam.com', 'baddomain.org']
     */
    public function forbiddenDomains(array $allowedDomains): self
    {
        return $this->addConstraint(new EmailFilterDomainsConstraint($allowedDomains, false));
    }

    /**
     * Restrict the email address to specific Top-Level Domains (TLDs).
     *
     * @param list<string> $allowedTlds Allowed TLDs like ['com', 'org', 'net']
     */
    public function allowedTlds(array $allowedTlds): self
    {
        return $this->addConstraint(new EmailFilterTldsConstraint($allowedTlds, true));
    }

    /**
     * Restrict the email address by forbidding specific Top-Level Domains (TLDs).
     *
     * @param list<string> $allowedTlds Forbidden TLDs like ['xyz', 'ru', 'spam']
     */
    public function forbiddenTlds(array $allowedTlds): self
    {
        return $this->addConstraint(new EmailFilterTldsConstraint($allowedTlds, false));
    }
}
