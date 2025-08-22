<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\KeyRule;
use Phithi92\TypedEnv\Exception\CastException;

final class EmailCasterTest extends TestCase
{
    public function testValidEmail(): void
    {
        $r = (new KeyRule('E'))->typeEmail();
        self::assertSame('user@example.com', $r->apply('user@example.com'));
    }

    public function testInvalidEmail(): void
    {
        $r = (new KeyRule('E'))->typeEmail();
        $this->expectException(CastException::class);
        $r->apply('bad@address@x');
    }
}
