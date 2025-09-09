<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\EmailCaster;
use Phithi92\TypedEnv\Exception\CastException;

final class EmailCasterTest extends TestCase
{
    public function testAcceptsSimpleEmail(): void
    {
        $caster = new EmailCaster();
        $this->assertSame('user@example.com', $caster->cast('E', 'user@example.com'));
    }

    public function testRejectsMissingDomain(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user@');
    }

    public function testAcceptsPlusAddressing(): void
    {
        $caster = new EmailCaster();
        $this->assertSame('user+tag@example.com', $caster->cast('E', 'user+tag@example.com'));
    }

    public function testAcceptsDotsInLocalPart(): void
    {
        $caster = new EmailCaster();
        $this->assertSame('user.name@example.com', $caster->cast('E', 'user.name@example.com'));
    }

    public function testAcceptsSubdomain(): void
    {
        $caster = new EmailCaster();
        $this->assertSame('user@mail.example.com', $caster->cast('E', 'user@mail.example.com'));
    }

    public function testAcceptsUppercase(): void
    {
        $caster = new EmailCaster();
        $this->assertSame('USER@EXAMPLE.COM', $caster->cast('E', 'USER@EXAMPLE.COM'));
    }

    public function testRejectsNoAtSymbol(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'userexample.com');
    }

    public function testRejectsEmptyLocalPart(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', '@example.com');
    }

    public function testRejectsDoubleAt(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user@@example.com');
    }

    public function testRejectsSpacesInLocalPart(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user example@example.com');
    }

    public function testRejectsSpacesInDomain(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user@exa mple.com');
    }

    public function testRejectsLeadingOrTrailingSpaces(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', ' user@example.com ');
    }

    public function testRejectsConsecutiveDotsInLocalPart(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user..dot@example.com');
    }

    public function testRejectsDomainStartingWithHyphen(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user@-example.com');
    }

    public function testRejectsDomainWithLeadingDot(): void
    {
        $this->expectException(CastException::class);
        (new EmailCaster())->cast('E', 'user@.example.com');
    }

    public function testAcceptsShortAddresses(): void
    {
        $caster = new EmailCaster();
        $this->assertSame('u@e.io', $caster->cast('E', 'u@e.io'));
    }
}
