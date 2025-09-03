<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Types;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Types\EmailKeyRule;
use Phithi92\TypedEnv\Exception\CastException;
use Phithi92\TypedEnv\Exception\ConstraintException;
use Phithi92\TypedEnv\Constraint\EmailAllowedDomainsConstraint;
use Phithi92\TypedEnv\Constraint\EmailAllowedTldsConstraint;

final class EmailKeyRuleTest extends TestCase
{
    public function testValidEmail(): void
    {
        $rule = new EmailKeyRule('ADMIN_EMAIL');
        $this->assertSame('admin@example.com', $rule->apply('admin@example.com'));
    }

    public function testInvalidEmailThrowsCast(): void
    {
        $this->expectException(CastException::class);
        $this->expectExceptionMessage("ENV ADMIN_EMAIL: 'not-an-email' is not a valid email");
        (new EmailKeyRule('ADMIN_EMAIL'))->apply('not-an-email');
    }

    public function testAllowedDomainsConstraint(): void
    {
        $rule = new EmailKeyRule('ADMIN_EMAIL');
        $rule->allowedDomains(['example.com','my.org']);

        $this->assertSame('user@example.com', $rule->apply('user@example.com'));

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('The email for "ADMIN_EMAIL" has an invalid domain');
        $rule->apply('user@evil.net');
    }

    public function testAllowedTldsConstraint(): void
    {
        $rule = new EmailKeyRule('SUPPORT_EMAIL');
        $rule->allowedTlds(['com','org']);
        $this->assertSame('support@company.org', $rule->apply('support@company.org'));

        $this->expectException(ConstraintException::class);
        $this->expectExceptionMessage('The email for "SUPPORT_EMAIL" has an invalid TLD');
        $rule->apply('support@company.dev');
    }
}
