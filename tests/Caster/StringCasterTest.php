<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Phithi92\TypedEnv\Caster\StringCaster;

final class StringCasterTest extends TestCase
{
    public function testStringCaster(): void
    {
        $caster = new StringCaster();
        $this->assertSame('hello', $caster->cast('S', 'hello'));
    }
}
